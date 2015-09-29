<?php

namespace Flysap\ModuleManager;

use Flysap\FileManager\FileManagerServiceProvider;
use Flysap\TableManager\TableServiceProvider;
use Illuminate\Support\ServiceProvider;
use Flysap\Support;

class ModuleServiceProvider extends ServiceProvider {

    /**
     * On boot's application load package requirements .
     */
    public function boot() {
        $this->loadRoutes()
            ->loadViews();

        $this->registerMenu();

        view()->share('total_modules', count(
            app('module-cache-manager')->getModules()
        ));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->registerProviders();

        $this->loadConfiguration();

        /** Module uploader . */
        $this->app->singleton('module-manager', ModuleManager::class);

        /** Register caching module . */
        $this->app->singleton('module-cache-manager', CacheManager::class);


        /**
         * There will be register all modules autoloaders . It is located in register function because
         *  autoloaders can be any service provider class so they must be registered before boot method .
         *
         */
        $modules = app('module-cache-manager')
            ->getModules();

        #@todo there is need to register autoloaders for generated modules.  ?? maybe ..)
        array_walk($modules, function(Module $module) {
            $module->registerAutoloaders();
        });

        /** Register module manager service layer . */
        $this->app->singleton('module-service', function($app) {
            return new ModuleService(
                $app['module-cache-manager'], $app['module-manager']
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

    /**
     * Register menu .
     *
     */
    protected function registerMenu() {
        $menuManager = app('menu-manager');

        $menuManager->addNamespace(realpath(__DIR__ . '/../'), true);
    }

    /**
     * There will be registered dependencies providers .
     *
     */
    protected function registerProviders() {
        $dependencies = [TableServiceProvider::class, FileManagerServiceProvider::class];

        array_walk($dependencies, function($dependency) {
            app()->register($dependency);
        });
    }
}