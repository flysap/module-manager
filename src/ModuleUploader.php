<?php

namespace Flysap\ModuleManger;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Module uploader processing ..
 *
 *  Will need to have an configuration file where will be listed store path for modules like:
 *     module_store: path_to_store
 *
 *  On installation module vendor:publish command will be called which will copy file to configuration folder
 *      and give the posibility to administrator to redact that file.. (Of sure all operations will be made from BE)
 *
 *  All the modules will have next folder structure
 *      --> folder
 *          --> vendor
 *              --> package
 *                     --> files
 *
 *  Before module installation need to check it for some standarts of .
 *
 *
 *
 * Class ModuleUploader
 * @package Flysap\ModuleManger
 */
class ModuleUploader {

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    public function __construct(FileSystem $fileSystem, Finder $finder) {
        $this->fileSystem = $fileSystem;
        $this->finder     = $finder;
    }

    /**
     * Upload module to modules path ..
     *
     * @param $module
     */
    public function upload($module) {

        #@todo
         /**
          * 1 . Check if module has specific format to be uploaded
          * 2. Check for require module.info file (Here will be described all data about current module like title, description, version)
          * 3. Check if store path from config file has access to write .
          * 4. Check if folder exists, if not create one ..
          * 5. Check if module hasn't uploaded yet (use finder)
          * 6. Copy file to store path directory
          * 7. Return path to stored path ..
          */
    }


}