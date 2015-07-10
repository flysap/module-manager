<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateModuleManagerTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('modules', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->decimal('version');
            $table->string('path');
            $table->tinyInteger('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('modules');
    }
}
