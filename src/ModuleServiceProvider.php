<?php

namespace Flysap\ModuleManager;

use Flysap\FileManager\FileManagerServiceProvider;
use Flysap\ModuleManager\Widgets\ModulesWidget;
use Parfumix\TableManager\TableServiceProvider;
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

        $totalModules = count(
            app('module-cache-manager')->getModules()
        );

        view()->share('total_modules', $totalModules);

        /** Register modules widget . */
        app('widget-manager')->addWidget('modules', function() use($totalModules) {
            return view('themes::widgets.uploads', ['value' => $totalModules, 'title' => 'Modules']);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->registerPackageServices();

        $this->loadConfiguration();

        /** Module uploader . */
        $this->app->singleton('module-manager', ModuleManager::class);

        /** Register caching module . */
        $this->app->singleton('module-cache-manager', CacheManager::class);

        /** Register module manager service layer . */
        $this->app->singleton('module-service', function($app) {
            return new ModuleService(
                $app['module-cache-manager'], $app['module-manager']
            );
        });

        /**
         * There will be register all modules autoloaders . It is located in register function because
         *  autoloaders can be any service provider class so they must be registered before boot method .
         *
         */
        $modules = app('module-cache-manager')
            ->getModules();

        array_walk($modules, function(Module $module) {
            $module->registerServiceProvider();
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
    protected function registerPackageServices() {
        $dependencies = [
            TableServiceProvider::class,
            FileManagerServiceProvider::class
        ];

        array_walk($dependencies, function($dependency) {
            app()->register($dependency);
        });
    }
}