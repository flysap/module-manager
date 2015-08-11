<?php

namespace Flysap\ModuleManager;

use Flysap\ModuleManager\Contracts\ModuleRepositoryContract;
use Flysap\ModuleManager\Contracts\ModuleServiceContract;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

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

        /** Module uploader . */
        $this->app->singleton('module-manager', function() {
           return new ModuleManager(
               new Finder()
           );
        });

        /** Register caching module . */
        $this->app->singleton('module-caching', function() {
            return new ModulesCaching(
                new Finder()
            );
        });

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
        $array = Yaml::parse(file_get_contents(
            __DIR__ . '/../configuration/general.yaml'
        ));

        $config = $this->app['config']->get('module-manager', []);

        $this->app['config']->set('module-manager', array_merge($array, $config));

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