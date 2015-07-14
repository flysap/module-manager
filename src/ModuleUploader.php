<?php

namespace Flysap\ModuleManger;

use Flysap\ModuleManger\Contracts\ConfigParserContract;
use Flysap\ModuleManger\Exceptions\ModuleUploaderException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

/**
 * Module uploader processing ..
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

    /**
     * @var array
     */
    protected $extensions = ['zip'];

    /**
     * @var ConfigParserContract
     */
    protected $configParser;

    /**
     * @var
     */
    protected $archiver;

    public function __construct(FileSystem $fileSystem, Finder $finder, ConfigParserContract $configParser) {
        $this->fileSystem = $fileSystem;
        $this->finder = $finder;
        $this->configParser = $configParser;
    }

    /**
     * Upload module to modules path ..
     *
     * @param UploadedFile $module
     * @return \Symfony\Component\HttpFoundation\File\File
     * @throws ModuleUploaderException
     */
    public function upload(UploadedFile $module) {
        $extension = null;

        if (! $extension = $module->guessClientExtension())
            $extension = $module->getClientOriginalExtension();


        if (! in_array($extension, $this->extensions))
            throw new ModuleUploaderException(
                _('Invalid module format.')
            );


        if(! $configuration = $this->getConfig($module))
            throw new ModuleUploaderException(
                _("Nof found module config file")
            );

        $path = $this->getStoragePath() . DIRECTORY_SEPARATOR . $configuration['general']['name'];

        $this->extract(
            $module, app_path( '../' . $path)
        );

        return array_merge(
            ['path' => $path], $configuration
        );
    }

    /**
     * Extract archive to specific path .
     *
     * @param $module
     * @param null $path
     * @return mixed | Path where module was uploaded .
     * @throws ModuleUploaderException
     */
    protected function extract($module, $path = null) {
        /** Check if path exists, other case create one . */
        if (! $this->fileSystem->exists($path) ) {
            $this->fileSystem->mkdir(
                $path
            );
        }

        $archiver = $this->getArchiver();
        $archiver->open($module);

        $archiver->extractTo(
            $path
        );

        return $path;
    }


    /**
     * Get storage path .
     *
     * @return mixed
     * @throws ModuleUploaderException
     */
    protected function getStoragePath() {
        $path = config('module-manager.module_path');

        if (! $path || $path == '')
            throw new ModuleUploaderException(
                _("Cannot fine storage path for modules.")
            );

        return $path;
    }

    /**
     * Get configuration file .
     *
     * @param UploadedFile $module
     * @return mixed
     */
    protected function getConfig(UploadedFile $module) {
        $archiver = $this->getArchiver();

        if ($archiver->open($module)) {

            $isFoundConfigFile = false;

            for ($i = 0; $i < $archiver->numFiles; $i++) {
                $stat = $archiver->statIndex($i);

                if (preg_match('/module.(\w{1,3})$/', $stat['name'])) {
                    $isFoundConfigFile = true;

                    $moduleFile = $stat['name'];
                }
            }

            if ($isFoundConfigFile)
                return $this->configParser->parse(
                    $archiver->getFromName($moduleFile)
                );
        }
    }

    /**
     * @return ZipArchive
     */
    protected function getArchiver() {
        if (! $this->archiver)
            $this->archiver = new ZipArchive();

        return $this->archiver;
    }

}