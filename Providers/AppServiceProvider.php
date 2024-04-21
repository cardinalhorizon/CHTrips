<?php

namespace Modules\CHTrips\Providers;

use App\Contracts\Modules\ServiceProvider;

/**
 * @package $NAMESPACE$
 */
class AppServiceProvider extends ServiceProvider
{
    private $moduleSvc;

    protected $defer = false;

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->moduleSvc = app('App\Services\ModuleService');

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        $this->registerLinks();

        // Uncomment this if you have migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        //
    }

    /**
     * Add module links here
     */
    public function registerLinks(): void
    {
        // Show this link if logged in
        $this->moduleSvc->addFrontendLink('Trips', '/trips', '', $logged_in=true);

        // Admin links:
        $this->moduleSvc->addAdminLink('Trips', '/admin/trips');
        $this->moduleSvc->addAdminLink('Missions', '/admin/missions');
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('chtrips.php'),
        ], 'chtrips');

        $this->mergeConfigFrom(__DIR__.'/../Config/config.php', 'chtrips');
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/chtrips');
        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([$sourcePath => $viewPath],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/chtrips';
        }, \Config::get('view.paths')), [$sourcePath]), 'chtrips');
    }

    /**
     * Register translations.
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/chtrips');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'chtrips');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'chtrips');
        }
    }
}
