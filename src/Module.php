<?php

namespace Flysap\ModuleManager;

class Module {

    /**
     * @var array
     */
    protected $attributes;

    public function __construct(array $attributes = array()) {
        $this->attributes = $attributes;
    }


    /**
     * Get module name .
     *
     * @return string
     */
    public function getName() {
        return isset($this->attributes['name']) ? $this->attributes['name'] : '';
    }

    /**
     * Get module description .
     *
     * @return string
     */
    public function getDescription() {
        return isset($this->attributes['description']) ? $this->attributes['description'] : '';
    }

    /**
     * Get module version .
     *
     * @return string
     */
    public function getVersion() {
        return isset($this->attributes['version']) ? $this->attributes['version'] : '';
    }

    /**
     * Get autoload files .
     *
     * @return string
     */
    public function getServiceProvider() {
        return isset($this->attributes['autoload']) ? $this->attributes['autoload'] : '';
    }

    /**
     * Check if has autoloader .
     *
     * @return bool
     */
    public function hasServiceProvider() {
        return isset($this->attributes['autoload']);
    }

    /**
     * @param bool $force
     * @param callable $register
     */
    public function registerServiceProvider($force = false, \Closure $register = null) {
        $canRegister = ($this->isDisabled() && $force) ? true : ($this->isDisabled()) ? false : true;

        if($canRegister && $this->hasServiceProvider()) {
            $autoloaders = (array)$this->getServiceProvider();

            array_walk($autoloaders, function($autoloader) use($register) {
                if( empty($autoloader))
                    return false;

                if( ! is_null($register) )
                    $register($autoloader);
                else
                    app()->register($autoloader);
            });
        }
    }

    /**
     * Is module disabled .
     *
     * @return bool
     */
    public function isDisabled() {
        return isset($this->attributes['disabled']);
    }


    /**
     * Get menu array .
     *
     * @return string
     */
    public function getMenu() {
        return isset($this->attributes['menu']) ? $this->attributes['menu'] : '';
    }

    /**
     * Return attributes .
     *
     * @return array
     */
    public function toArray() {
        return $this->attributes;
    }
}