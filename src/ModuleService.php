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

    /**
     * @var ModuleUploader
     */
    private $moduleUploader;

    public function __construct(ModuleRepositoryContract $moduleRepository, ModuleUploader $moduleUploader) {
        $this->moduleRepository = $moduleRepository;
        $this->moduleUploader = $moduleUploader;
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

            $this->moduleRepository
                ->create([
                   'name'   => $configuration['name'],
                   'path'   => $configuration['path'],
                   'active' => $configuration['version']
                ]);
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
        $modules = $this->moduleRepository
            ->modules();

        return $modules;
    }

}