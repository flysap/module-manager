<?php

namespace Flysap\ModuleManger;

use Flysap\ModuleManger\Contracts\ModuleRepositoryContract;
use Flysap\ModuleManger\Contracts\ModuleServiceContract;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Filesystem\Filesystem;
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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        /** Register module manager repository . */
        $this->app->singleton(ModuleRepositoryContract::class, function() {
            return new ModuleRepository();
        });

        /** Module uploader . */
        $this->app->singleton('module-uploader', function() {
           return new ModuleUploader(
               new Filesystem(), new Finder(), new ParserIni()
           );
        });

        /** Register module manager service layer . */
        $this->app->singleton(ModuleServiceContract::class, function($app) {
            return new ModuleService(
                $app[ModuleRepositoryContract::class], $app['module-uploader']
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
            __DIR__ . '/../resources/configuration/general.yaml'
        ));

        $config = $this->app['config']->get('module-manager', []);

        $this->app['config']->set('module-manager', array_merge($array, $config));

        return $this;
    }

    /**
     * Load views .
     */
    protected function loadViews() {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-manager');

        return $this;
    }
}