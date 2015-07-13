<?php

use Flysap\ModuleManger\Contracts\ModuleServiceContract;
use Illuminate\Http\Request;

Route::group(['prefix' => 'module-manager'], function() {

    /**
     * Route for module installation
     */
    Route::match(['post', 'get'], '/upload', ['as' => 'module-upload', function(Request $request) {
        $service = app(ModuleServiceContract::class);

        if( $request->method() == 'POST' )
            if( $service->install(
                $request->file('module')
            ) )
                return redirect()
                    ->back();

        return view('module-manager::upload');
    }]);

    Route::get('list/{page?}', function() {
        $service = app(ModuleServiceContract::class);

        return $service->show();
    });

    Route::get('remove/{module}', function() {
        $service = app(ModuleServiceContract::class);

        return $service->remove();
    });
});