<?php

namespace Flysap\ModuleManger;

use Illuminate\Database\Eloquent\Model;

class ModuleRepository extends Model {

    public $table = 'modules';

    public $timestamps = false;

    public function modules() {}

}