<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'module-manager'], function() {

    Route::match(['post', 'get'], '/upload', ['as' => 'module-upload', function(Request $request) {
        $service = app('module-service');

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

        $service = app('module-service');

        return $service->lists();

    }]);

    Route::get('edit/{module}', ['as' => 'module-edit', function($module) {

        $service = app('module-service');

        return $service->edit($module);

    }])->where(['module' => "^(.)*"]);

    Route::get('remove/{module}', ['as' => 'module-remove', function($module) {

        return app('module-service')
            ->remove($module);

    }])->where(['module' => "^(.)*"]);
});