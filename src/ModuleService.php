<?php

namespace Flysap\ModuleManager;

use Flysap\ModuleManager\Contracts\ModuleServiceContract;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Flysap\TableManager;

class ModuleService implements ModuleServiceContract {

    private $modulesCaching;

    private $moduleManager;

    public function __construct(ModulesCaching $modulesCaching, ModuleManager $moduleManager) {
        $this->modulesCaching = $modulesCaching;
        $this->moduleManager = $moduleManager;
    }

    public function install(UploadedFile $module) {
        if( $configuration = $this->moduleManager
            ->upload($module) ) {

            $this->modulesCaching
                ->flush();

            return true;
        }
    }

    public function edit($module) {

    }

    public function upgrade() {
        // TODO: Implement upgrade() method.
    }

    public function remove($module) {
        $this->moduleManager
            ->remove($module);

        $this->modulesCaching
            ->flush();

        return redirect()
            ->back();
    }

    public function lists() {
        $modules = $this->modulesCaching
            ->toArray();

        $table = TableManager\table('Collection', array(
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
        ), ['class' => 'table table-bordered table-striped dataTable']);

        return view('module-manager::lists', compact('table'));
    }
}