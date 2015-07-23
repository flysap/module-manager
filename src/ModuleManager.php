<?php

namespace Flysap\ModuleManager;

use Flysap\ModuleManager\Exceptions\ModuleUploaderException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;
use Flysap\Support;

/**
 * Module uploader processing ..
 *
 * Class ModuleUploader
 * @package Flysap\ModuleManger
 */
class ModuleManager {

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * @var array
     */
    protected $extensions = ['zip'];

    /**
     * @var
     */
    protected $archiver;

    public function __construct(Finder $finder) {
        $this->finder = $finder;
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


        if(! $configuration = $this->getConfiguration($module))
            throw new ModuleUploaderException(
                _("Nof found module config file")
            );


        $path = $this->getStoragePath() . DIRECTORY_SEPARATOR . $configuration['name'];

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
        if (! Support\is_path_exists($path) )
            Support\mk_path($path);

        $archiver = $this->getArchiver();
        $archiver->open($module);

        $archiver->extractTo(
            $path
        );

        return $path;
    }

    /**
     * Remove module ..
     *
     * @param $module
     * @return $this
     * @throws ModuleUploaderException
     */
    public function remove($module) {
        list($vendor, $name) = explode('-', $module);

        $path = $this->getStoragePath();

        $fullPath = app_path('../' . $path . DIRECTORY_SEPARATOR);

        if( Support\is_path_exists($fullPath . $vendor . DIRECTORY_SEPARATOR . $name) )
            Support\remove_paths(
                $fullPath . $vendor . DIRECTORY_SEPARATOR . $name
            );

        if( Support\is_folder_empty($fullPath . $vendor) )
            Support\remove_paths(
                $fullPath . $vendor
            );

        return $this;
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
    protected function getConfiguration(UploadedFile $module) {
        $archiver = $this->getArchiver();

        if ($archiver->open($module)) {

            for ($i = 0; $i < $archiver->numFiles; $i++) {
                $stat = $archiver->statIndex($i);

                if (preg_match('/module.(\w{1,4})$/', $stat['name'], $matches)) {
                    $moduleFile = $stat['name'];

                    $parser = ParserFactory::factory($matches[1]);

                    return $parser->parse(
                        $archiver->getFromName($moduleFile)
                    );
                }
            }
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