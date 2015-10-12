<?php

namespace Flysap\ModuleManager;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Parfumix\TableManager;
use Flysap\Support;

class ModuleService {

    protected $cacheManager;

    protected $moduleManager;

    public function __construct(CacheManager $cacheManager, ModuleManager $moduleManager) {
        $this->cacheManager  = $cacheManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Install module .
     *
     * @param UploadedFile $file
     * @return bool
     * @throws Exceptions\ModuleUploaderException
     */
    public function install(UploadedFile $file) {
        if( $module = $this->moduleManager->upload($file) ) {

            if( $module->hasAutoloader()  )
                Support\artisan('vendor:publish', [
                    '--provider' => $module->getAutoloader()
                ]);

            /** By default we have to run main migrations which are important . */
            Support\artisan('migrate');

            /** We need to run module migration */
            Support\artisan('migrate', [
                '--path' => app_path(config('module-manager.module_path') . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'migrations')
            ]);

            /** If there is a seed files we have to run them  */
            Support\artisan('db:seed');


            /** Refresh cache files . */
            $this->cacheManager
                ->flush();

            return true;
        }
    }

    public function edit($module) {
        list($vendor, $name) = explode('/', $module);

        $path = config('module-manager.module_path');

        $pathModule = $path . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . $name;

        return view('module-manager::edit', compact('pathModule'));
    }

    public function upgrade() {
        // TODO: Implement upgrade() method.
    }

    public function remove($module) {
        $this->moduleManager
            ->remove($module);

        $this->cacheManager
            ->flush();

        return redirect()
            ->back();
    }

    /**
     * @return \Illuminate\View\View
     */
    public function lists() {
        $modules = $this->cacheManager
            ->findModules();

        $table = TableManager\table(array(
            'columns' => array('name' => ['closure' => function($value) {

                $edit = route('module-edit', ['module' => $value]);
                $delete = route('module-remove', ['module' => $value]);

                $template = <<<HTML
$value
<div class="tools">
    <a href="$edit"><i class="fa fa-edit"></i></a>
    <a href="$delete"><i class="fa fa-trash-o"></i></a>
</div>
HTML;
                return $template;

            }],'description','version'),
            'rows'    => $modules
        ), 'collection', ['class' => 'table table-bordered table-striped dataTable']);

        return view('module-manager::lists', compact('table'));
    }
}