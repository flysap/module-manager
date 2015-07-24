<?php

namespace Flysap\ModuleManager;

use Flysap\ModuleManager\Exceptions\ModuleUploaderException;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Finder\Finder;
use Flysap\Support;
class ModulesCaching implements Arrayable {

    const CACHE_FILE = 'modules.json';

    /**
     * @var
     */
    private $finder;

    public function __construct(Finder $finder) {

        $this->finder = $finder;
    }

    /**
     * Walk through module ini files parse and cache it ..
     */
    public function flush() {
        $modules = $this->findModulesConfig();

        $fullCachePath = $this->getCachePath();

        if(! Support\is_path_exists($fullCachePath))
            Support\mk_path($fullCachePath);

        Support\dump_file($fullCachePath . DIRECTORY_SEPARATOR . self::CACHE_FILE, json_encode($modules));

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

        if( Support\is_path_exists($fullCachePath) )
            Support\remove_paths([
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

        if( ! Support\is_path_exists($fullCachePath . DIRECTORY_SEPARATOR . self::CACHE_FILE))
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
     * @param null $paths
     * @param array $keys
     * @return array
     * @throws ModuleUploaderException
     */
    public function findModulesConfig($paths = null, $keys = array()) {
        $name     = '/module.(\w{1,4})$/';

        if( is_null($paths) )
            $paths = $this->getStoragePath(true);

        if(! is_array($paths))
            $paths = (array)$paths;

        $modules = [];
        $finder  = $this->finder;

        $finder->name($name)
            ->depth('< 3');

        array_walk($paths, function($path) use(& $finder, & $modules) {
            foreach ($finder->in($path) as $file) {

                $parser = ParserFactory::factory(
                    $file->getExtension()
                );

                $module = $parser
                    ->parse( $file->getContents() );

                if( isset($module['name']) )
                    $modules[$module['name']] = $module;
            }
        });

        if(! is_array($keys))
            $keys = (array)$keys;

        return array_only($modules, $keys ? $keys : array_keys($modules));
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