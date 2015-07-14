<?php

namespace Flysap\ModuleManger;

use Flysap\ModuleManger\Contracts\ConfigParserContract;
use Flysap\ModuleManger\Exceptions\ModuleUploaderException;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ModulesCaching implements Arrayable {

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

        $fullCachePath = $this->getCachePath();

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
        $fullCachePath = $this->getCachePath();

        if( $this->filesystem->exists( $fullCachePath))
            $this->filesystem->remove([
                $fullCachePath . DIRECTORY_SEPARATOR . self::CACHE_FILE
            ]);

        return $this;
    }

    /**
     * Get array cached ..
     *
     * @param array $modules
     * @return mixed
     * @throws ModuleUploaderException
     */
    public function toArray(array $modules = array()) {
        $fullCachePath = $this->getCachePath(true);

        if( ! $this->filesystem->exists( $fullCachePath . DIRECTORY_SEPARATOR . self::CACHE_FILE ))
            $this->flush();

        $cache = file_get_contents(
            $fullCachePath . DIRECTORY_SEPARATOR . self::CACHE_FILE
        );

        $cache = json_decode($cache, true);

        if( $modules )
            return array_intersect_key($cache, array_flip((array) $modules));

        return $cache;
    }

    /**
     * Find modules configuration files .
     *
     * @return array
     * @throws ModuleUploaderException
     */
    protected function findModulesConfig() {
        $name     = '/module.(\w{1,3})$/';
        $fullPath = $this->getStoragePath(true);

        $modules = [];
        $finder  = $this->finder;

        $finder->name($name)
            ->depth('< 3');

        foreach ($finder->in($fullPath) as $file) {
            $module = $this->configParser
                ->parse( $file->getContents() );

            if( isset($module['general']['name']) )
                $modules[$module['general']['name']] = $module;
        }

        return $modules;
    }

    /**
     * Get storage path .
     *
     * @return mixed
     * @throws ModuleUploaderException
     */
    protected function getStoragePath($asFull = true) {
        $path = config('module-manager.module_path');

        if (! $path || $path == '')
            throw new ModuleUploaderException(
                _("Cannot fine storage path for modules.")
            );

        if($asFull)
            $path = app_path('..' . DIRECTORY_SEPARATOR . $path);

        return $path;
    }

    /**
     * Get cache path .
     *
     * @return mixed
     * @throws ModuleUploaderException
     */
    protected function getCachePath($asFull = true) {
        $path = config('module-manager.cache_dir');

        if (! $path || $path == '')
            throw new ModuleUploaderException(
                _("Cannot fine cache path for modules.")
            );

        if( $asFull )
            $path = app_path(
                '..' . DIRECTORY_SEPARATOR . $path
            );

        return $path;
    }

}