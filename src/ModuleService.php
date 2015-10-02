<?php

namespace Flysap\ModuleManager;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Parfumix\TableManager;

class ModuleService {

    protected $cacheManager;

    protected $moduleManager;

    public function __construct(CacheManager $cacheManager, ModuleManager $moduleManager) {
        $this->cacheManager  = $cacheManager;
        $this->moduleManager = $moduleManager;
    }

    public function install(UploadedFile $module) {
        if( $configuration = $this->moduleManager
            ->upload($module) ) {

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