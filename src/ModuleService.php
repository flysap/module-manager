<?php

namespace Flysap\ModuleManager;

use Flysap\ModuleManager\Contracts\ModuleServiceContract;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ModuleService implements ModuleServiceContract {

    /**
     * @var ModulesCaching
     */
    private $modulesCaching;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    public function __construct(ModulesCaching $modulesCaching, ModuleManager $moduleManager) {
        $this->modulesCaching = $modulesCaching;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Install module ..
     *
     * @param UploadedFile $module
     * @return mixed
     * @throws Exceptions\ModuleUploaderException
     */
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

    /**
     * Upgrade module .
     *
     * @return mixed
     */
    public function upgrade() {
        // TODO: Implement upgrade() method.
    }

    /**
     * Remove module ..
     *
     * @param $module
     * @return mixed
     */
    public function remove($module) {
        $this->moduleManager
            ->remove($module);

        $this->modulesCaching
            ->flush();

        return redirect()
            ->back();
    }

    /**
     * Show list of modules .
     *
     * @return mixed
     */
    public function modules() {
        $modules = $this->modulesCaching
            ->toArray();

        return $modules;
    }
}