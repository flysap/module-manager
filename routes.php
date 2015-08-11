<?php

use Flysap\ModuleManager\Contracts\ModuleServiceContract;
use Illuminate\Http\Request;

Route::group(['prefix' => 'module-manager'], function() {

    Route::match(['post', 'get'], '/upload', ['as' => 'module-upload', function(Request $request) {
        $service = app(ModuleServiceContract::class);

        if( $request->method() == 'POST' ) {
            if( $service->install(
                $request->file('module')
            ) ) {
                return redirect(
                    route('module-lists')
                );
            }
        }

        return view('module-manager::upload');
    }]);

    Route::get('lists/{page?}', ['as' => 'module-lists', function() {
        $service = app(ModuleServiceContract::class);

        $modules = $service->modules();

        return view('module-manager::lists', ['data' => $modules, 'fields' => [
            'Name','Description','Version'
        ]]);
    }]);

    Route::get('edit/{module}', ['as' => 'module-edit', function() {

    }]);

    Route::get('remove/{module}', ['as' => 'module-remove', function($module) {
        return app(ModuleServiceContract::class)
            ->remove($module);
    }]);
});