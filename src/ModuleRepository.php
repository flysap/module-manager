<?php

namespace Flysap\ModuleManger;

use Illuminate\Database\Eloquent\Model;

class ModuleRepository extends Model {

    use ScopeTrait;

    public $table = 'modules';

    public $timestamps = false;

    /**
     * Get all modules .
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function modules() {
        return $this
            ->all();
    }

    /**
     * Is module installed .
     *
     * @param $name
     * @return mixed
     */
    public function isInstalled($name) {
        return $this->ofName($name)
            ->first();
    }

}