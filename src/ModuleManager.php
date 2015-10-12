<?php

namespace Flysap\ModuleManager;

use Flysap\ModuleManager\Exceptions\ModuleUploaderException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;
use Flysap\Support;

class ModuleManager {

    /**
     * @var array
     */
    protected $extensions = ['zip'];

    /**
     * @var
     */
    protected $archiver;

    /**
     * Upload module to modules path ..
     *
     * @param UploadedFile $module
     * @return Module
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

        $path = config('module-manager.module_path') . DIRECTORY_SEPARATOR . $configuration['name'];

        if( Support\is_path_exists(app_path($path)) )
            throw new ModuleUploaderException(
                _('Module already exists.')
            );

        $this->extract(
            $module, app_path( '../' . $path)
        );

        return (new Module($configuration));
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
        $fullPath = $this->getModuleFullPath($module);

        if( Support\is_path_exists($fullPath) )
            Support\remove_paths(
                $fullPath
            );

        return $this;
    }


    /**
     * Get module full path .
     *
     * @param $module
     * @return string
     * @throws ModuleUploaderException
     */
    public function getModuleFullPath($module) {
        list($vendor, $name) = explode('/', $module);

        $path = config('module-manager.module_path');

        $fullPath = app_path('../' . $path . DIRECTORY_SEPARATOR);

        return $fullPath . $vendor . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * Get configuration file .
     *
     * @param UploadedFile $module
     * @return mixed
     */
    public function getConfiguration(UploadedFile $module) {
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