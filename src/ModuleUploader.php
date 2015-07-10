<?php

namespace Flysap\ModuleManger;

use Flysap\ModuleManger\Exceptions\ModuleUploaderException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

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

    const TMP_UPLOAD = 'app/modules';

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * @var array
     */
    protected $extensions = ['zip'];

    public function __construct(FileSystem $fileSystem, Finder $finder) {
        $this->fileSystem = $fileSystem;
        $this->finder     = $finder;
    }

    /**
     * Upload module to modules path ..
     *
     * @param UploadedFile $module
     * @return \Symfony\Component\HttpFoundation\File\File
     * @throws ModuleUploaderException
     */
    public function upload(UploadedFile $module) {

        $exntension = ! is_null( $module->guessClientExtension() ) ? $module->guessClientExtension() : $module->getClientOriginalExtension();

        if(! in_array( $exntension, $this->extensions ))
            throw new ModuleUploaderException(
                _('Invalid module format.')
            );

        $this->validate($module);

        $storagePath = $this->getStoragePath();

        /** Check if path exists, other case create one . */
        /*if( $this->fileSystem->exists(
            $storagePath
        ) )
            $this->fileSystem->mkdir(
                $storagePath
            );

        $this->fileSystem->chmod(
            $storagePath, 0777, false
        );


        if( ! $uploaded = $module->move(
            $storagePath
        ) )
            throw new ModuleUploaderException(_("Error on upload module."));

        $uploaded = $this->extract(
            $uploaded
        );*/

        return 1;

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

    /**
     * Get storage path .
     *
     * @return mixed
     * @throws ModuleUploaderException
     */
    protected function getStoragePath() {
        $path = config('module-manager.module_path');

        if(! $path || $path == '' )
            throw new ModuleUploaderException(
                _("Cannot fine storage path for modules.")
            );

        return $path;
    }

    /**
     * Validate module .
     *
     * @param UploadedFile $module
     * @return bool
     * @throws ModuleUploaderException
     */
    protected function getModuleConfiguration(UploadedFile $module) {
        $zip = new ZipArchive();

        /**
         * 1. go into the first level folder and find file module.ini
         * 2. parse that file and check for name.
         * 3. use filesystem object to check for that directory if that module isn't instsalled already
         * 4. extract archive directly to vendor_name
         */

        if ($zip->open($module)) {

            $isFoundConfigFile = false;

            for( $i = 0; $i < $zip->numFiles; $i++ ) {
                $stat = $zip->statIndex( $i );

                if( preg_match('/module.ini/', $stat['name'] ) ) {
                    $isFoundConfigFile = true;

                    $moduleFile = $stat['name'];
                }
            }

            if(! $isFoundConfigFile)
                throw new ModuleUploaderException(
                    _("Nof found module config file")
                );




            $fileModule = parse_ini_string(
                $zip->getFromName($moduleFile)
            );

            $isModuleFileExists = $zip->extractTo(
                app_path('../' . $this->getStoragePath() . DIRECTORY_SEPARATOR . $fileModule['name'])
            );

            $zip->close();

            if( ! $isModuleFileExists )
                throw new ModuleUploaderException(
                    _("Module file mismatch.")
                );

            return true;
        }

        throw new ModuleUploaderException(
            _("Archive open error")
        );
    }

    /**
     * Extract module archive .
     *
     * @param $uploaded
     * @param null $path
     */
    protected function extract($uploaded, $path = null) {
        return $uploaded;
    }

}