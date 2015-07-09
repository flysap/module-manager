<?php

namespace Flysap\ModuleManger;

use Flysap\ModuleManger\Contracts\ModuleRepositoryContract;
use Flysap\ModuleManger\Contracts\ModuleServiceContract;

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
    public function install() {
        // TODO: Implement install() method.
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

}