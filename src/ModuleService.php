<?php

namespace Flysap\ModuleManger;

use Flysap\ModuleManger\Contracts\ModuleRepositoryContract;
use Flysap\ModuleManger\Contracts\ModuleServiceContract;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ModuleService implements ModuleServiceContract {

    /**
     * @var ModuleUploader
     */
    private $moduleUploader;

    /**
     * @var ModulesCaching
     */
    private $modulesCaching;

    public function __construct(ModulesCaching $modulesCaching, ModuleUploader $moduleUploader) {
        $this->moduleUploader = $moduleUploader;
        $this->modulesCaching = $modulesCaching;
    }

    /**
     * Install module ..
     *
     * @param UploadedFile $module
     * @return mixed
     * @throws Exceptions\ModuleUploaderException
     */
    public function install(UploadedFile $module) {
        if( $configuration = $this->moduleUploader
            ->upload($module) ) {

            $this->modulesCaching
                ->flush();

            return true;
        }
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
     * @return mixed
     */
    public function remove() {
        // TODO: Implement remove() method.
    }

    /**
     * Show list of modules .
     *
     * @return mixed
     */
    public function modules() {
        return [];
    }

}