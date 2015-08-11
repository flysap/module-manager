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

        return $service->lists();
    }]);

    Route::get('edit/{module}', ['as' => 'module-edit', function($module) {
        $service = app(ModuleServiceContract::class);

        return $service->edit($module);
    }]);

    Route::get('remove/{module}', ['as' => 'module-remove', function($module) {
        return app(ModuleServiceContract::class)
            ->remove($module);
    }]);
});