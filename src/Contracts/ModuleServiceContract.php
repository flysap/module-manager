<?php

namespace Flysap\ModuleManager\Contracts;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ModuleServiceContract {

    /**
     * Install module ..
     *
     * @return mixed
     */
    public function install(UploadedFile $module);

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
    public function remove($module);
}