<?php

namespace Flysap\ModuleManger;

use Flysap\ModuleManger\Contracts\ModuleRepositoryContract;
use Illuminate\Database\Eloquent\Model;

class ModuleRepository extends Model implements ModuleRepositoryContract {

    use ScopeTrait;

    public $table = 'modules';

    public $timestamps = false;

    public $fillable = ['name', 'version', 'path', 'is_active'];

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