<?php

namespace Flysap\ModuleManager;

use Flysap\ModuleManager\Contracts\ModuleServiceContract;
use Flysap\TableManager\TableServiceProvider;
use Illuminate\Support\ServiceProvider;
use Flysap\Support;

class ModuleServiceProvider extends ServiceProvider {

    /**
     * On boot's application load package requirements .
     */
    public function boot() {
        $this->loadRoutes()
            ->loadConfiguration()
            ->loadViews();

        view()->share('total_modules', count(
            app('module-caching')->toArray()
        ));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $dependencies = [TableServiceProvider::class];

        array_walk($dependencies, function($dependency) {
            app()->register($dependency);
        });

        /** Module uploader . */
        $this->app->singleton('module-manager', ModuleManager::class);

        /** Register caching module . */
        $this->app->singleton('module-caching', ModulesCaching::class);


        /** Register module manager service layer . */
        $this->app->singleton(ModuleServiceContract::class, function($app) {
            return new ModuleService(
                $app['module-caching'], $app['module-manager']
            );
        });

    }

    /**
     * Load routes .
     *
     * @return $this
     */
    protected function loadRoutes() {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../routes.php';
        }

        return $this;
    }

    /**
     * Load configuration .
     *
     * @return $this
     */
    protected function loadConfiguration() {
        Support\set_config_from_yaml(
            __DIR__ . '/../configuration/general.yaml' , 'module-manager'
        );

        return $this;
    }

    /**
     * Load views .
     */
    protected function loadViews() {
        $this->loadViewsFrom(__DIR__.'/../views', 'module-manager');

        return $this;
    }
}