<?php

namespace Flysap\ModuleManger;

use Flysap\ModuleManger\Contracts\ModuleRepositoryContract;
use Flysap\ModuleManger\Contracts\ModuleServiceContract;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ModuleService implements ModuleServiceContract {

    /**
     * @var Contracts\ModuleRepositoryContract
     */
    protected $moduleRepository;

    public function __construct(ModuleRepositoryContract $moduleRepository) {
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * Install module ..
     *
     * @return mixed
     */
    public function install(UploadedFile $module) {
        /**
         * Get the name from module info
         * Check if that name is installed in database
         * Throw an exception if is installed
         * Call ModuleUploader to upload to specific folder
         * Register it to database
         * Mark as inactive.
         */
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
    public function show() {
        $modules = $this->moduleRepository
            ->modules();

        return $modules;
    }

}