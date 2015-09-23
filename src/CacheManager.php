<?php

namespace Flysap\ModuleManager;

use Flysap\ModuleManager\Exceptions\ModuleUploaderException;
use Symfony\Component\Finder\Finder;
use Flysap\Support;

#@todo it have to be refactored.. cache manager .

class CacheManager {

    const CACHE_FILE = 'modules.json';

    /**
     * @var
     */
    protected $modules;

    public function __construct() {
        $this->setModules();
    }


    /**
     * Set modules .
     *
     * @param array $modules
     * @return $this
     */
    public function setModules(array $modules = array()) {
        $foundModules = $this->findModules(null, $modules);

        $modules = [];
        foreach ($foundModules as $key => $module)
            $modules[$key] = (new Module($module));

        $this->modules = $modules;

        return $this;
    }

    /**
     * Get modules .
     *
     * @return array
     */
    public function getModules() {
        return $this->modules;
    }


    /**
     * Flush
     *
     * @return $this
     */
    public function flush() {
        $modules = $this->findModules();

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
     * Get cache path .
     *
     * @param bool $full
     * @return mixed
     * @throws ModuleUploaderException
     */
    protected function getCachePath($full = true) {
        $path = config('module-manager.cache_dir');

        if( $full )
            $path = app_path(
                '..' . DIRECTORY_SEPARATOR . $path
            );

        return $path;
    }

    /**
     * Find installed modules .
     *
     * @param null $paths
     * @param array $only
     * @return array
     */
    public function findModules($paths = null, $only = array()) {
        $name     = '/module.(\w{1,4})$/';

        if( is_null($paths) )
            $paths = realpath(app_path('../' . config('module-manager.module_path')));

        if(! is_array($paths))
            $paths = (array)$paths;

        $modules = [];
        $finder  = new Finder;

        $finder->name($name)
            ->depth('<= 3');

        foreach($paths as $path) {
            foreach ($finder->in($path) as $file) {
                $parser = ParserFactory::factory(
                    $file->getExtension()
                );

                $module = $parser
                    ->parse( $file->getContents() );

                if( isset($module['name']) )
                    $modules[$module['name']] = $module;
            }
        }

        if(! is_array($only))
            $only = (array)$only;

        $modules = array_only($modules, $only ? $only : array_keys($modules));

        return $modules;
    }

}