<?php

namespace Flysap\ModuleManger;

use Flysap\ModuleManger\Contracts\ConfigParserContract;
use Flysap\ModuleManger\Exceptions\ModuleUploaderException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ModulesCaching {

    const CACHE_FILE = 'modules.json';

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ConfigParserContract
     */
    private $configParser;

    public function __construct(Finder $finder, Filesystem $filesystem, ConfigParserContract $configParser) {

        $this->finder = $finder;
        $this->filesystem = $filesystem;
        $this->configParser = $configParser;
    }

    /**
     * Walk through module ini files parse and cache it ..
     */
    public function flush() {
        $modules = $this->findModulesConfig();

        $fullCachePath = app_path(
            '..' . DIRECTORY_SEPARATOR . $this->getCachePath()
        );

        if(! $this->filesystem->exists( $fullCachePath))
            $this->filesystem->mkdir(
                $fullCachePath
            );

        $this->filesystem
            ->dumpFile($fullCachePath . DIRECTORY_SEPARATOR . self::CACHE_FILE, json_encode($modules));

        return $this;
    }

    /**
     * Clear cache file .
     *
     * @return $this
     * @throws ModuleUploaderException
     */
    public function clear() {
        $fullCachePath = app_path(
            '..' . DIRECTORY_SEPARATOR . $this->getCachePath()
        );

        if( $this->filesystem->exists( $fullCachePath))
            $this->filesystem->remove([
                $fullCachePath . DIRECTORY_SEPARATOR . self::CACHE_FILE
            ]);

        return $this;
    }

    /**
     * Find modules configuration files .
     *
     * @return array
     * @throws ModuleUploaderException
     */
    protected function findModulesConfig() {
        $name     = '/module.(\w{1,3})$/';
        $fullPath = app_path('..' . DIRECTORY_SEPARATOR . $this->getStoragePath());

        $modules = [];
        $finder  = $this->finder;

        $finder->name($name)
            ->depth('< 3');

        foreach ($finder->in($fullPath) as $file) {
            $module = $this->configParser
                ->parse( $file->getContents() );

            if( isset($module['name']) )
                $modules[$module['name']] = $module;
        }

        return $modules;
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
     * Get cache path .
     *
     * @return mixed
     * @throws ModuleUploaderException
     */
    protected function getCachePath() {
        $path = config('module-manager.cache_dir');

        if (! $path || $path == '')
            throw new ModuleUploaderException(
                _("Cannot fine cache path for modules.")
            );

        return $path;
    }

}