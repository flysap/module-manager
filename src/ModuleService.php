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

            /**
             * When is installed it have to copy files to specific location
             *  1. but for the first it have to check if module with that name already exists
             *  2. it have to copy all the files to specific directory
             *  3. it have publish all module assets like migrations or seeds
             *  4. it have to run all the migrations and seeds if they exists .
             */

            if( $module->hasServiceProvider()  )
                Support\artisan('vendor:publish', [
                    '--provider' => $module->getServiceProvider()
                ]);

            /** By default we have to run main migrations which are important . */
            Support\artisan('migrate');

            /** If there is a seed files we have to run them  */
            Support\artisan('db:seed');

            /** Refresh cache files . */
            $this->cacheManager
                ->flush();

            return true;
        }
    }

    /**
     * Edit module .
     *
     * @param $module
     * @return \Illuminate\View\View
     */
    public function edit($module) {
        list($vendor, $name) = explode('/', $module);

        $path = config('module-manager.module_path');

        $pathModule = $path . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . $name;

        return view('module-manager::edit', compact('pathModule'));
    }

    /**
     * Remove module .
     *
     * @param $module
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove($module) {

        /**
         * Remove module have to delete module files and all the assets
         *
         *  1. it have to delete database migrations
         *  2. it have to delete migration files
         *  3. it have to delete seeds files
         */

        $allModules = $this->cacheManager->findModules();

        if( isset($allModules[$module]) ) {
            $module = new Module($allModules[$module]);

            if( $path = $this->moduleManager->getModuleFullPath( $module->getName() ) ) {
                if( Support\is_path_exists($path . DIRECTORY_SEPARATOR . 'migrations') ) {
                    #@todo .
                }
            }
        }

        $this->moduleManager
            ->remove($module->getName());

        $this->cacheManager
            ->flush();

        return redirect()
            ->back();
    }

    /**
     * Lists modules .
     *
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