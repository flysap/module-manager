<?php

use Flysap\ModuleManger\Contracts\ModuleServiceContract;
use Illuminate\Http\Request;

Route::group(['prefix' => 'module-manager'], function() {

    /**
     * Route for module installation
     */
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

    /**
     * Route for lists module ..
     */
    Route::get('lists/{page?}', ['as' => 'module-lists', function() {
        $service = app(ModuleServiceContract::class);

        $modules = $service->modules();

        return view('module-manager::lists', ['modules' => $modules]);
    }]);

    /**
     * Route for remove module .
     *
     */
    Route::get('remove/{module}', function() {
        $service = app(ModuleServiceContract::class);

        return $service->remove();
    });
});