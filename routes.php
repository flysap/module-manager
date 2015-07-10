<?php

use Flysap\ModuleManger\Contracts\ModuleServiceContract;

Route::group(['prefix' => 'module-manager'], function() {

    /**
     * Route for module installation
     */
    Route::post('upload', function() {
        $service = app(ModuleServiceContract::class);

        return $service->install();
    });

    Route::get('list/{page?}', function() {
        $service = app(ModuleServiceContract::class);

        return $service->list();
    });

    Route::get('remove/{module}', function() {
        $service = app(ModuleServiceContract::class);

        return $service->remove();
    });
});