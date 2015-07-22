<?php

namespace Flysap\ModuleManager;

trait ScopeTrait {

    /**
     * Get all active modules .
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query) {
        return $query
            ->where('active', 1);
    }

    /**
     * Get all inactive modules .
     *
     * @param $query
     * @return mixed
     */
    public function scopeInactive($query) {
        return $query
            ->where('active', 0);
    }

    /**
     * Get module by name .
     *
     * @param $query
     * @param $name
     * @return mixed
     */
    public function scopeOfName($query, $name) {
        return $query->where('name', $name);
    }
}