<?php

namespace Flysap\ModuleManger\Contracts;

interface ModuleServiceContract {

    /**
     * Install module ..
     *
     * @return mixed
     */
    public function install();

    /**
     * Upgrade module .
     *
     * @return mixed
     */
    public function upgrade();

    /**
     * Remove module ..
     *
     * @return mixed
     */
    public function remove();
}