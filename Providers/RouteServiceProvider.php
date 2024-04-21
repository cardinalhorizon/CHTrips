<?php

namespace Modules\CHTrips\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\CHTrips\Http\Controllers\Admin\TripsAdminController;
use Modules\CHTrips\Http\Controllers\Admin\TripTemplatesAdminController;
use Modules\CHTrips\Http\Controllers\Frontend\IndexController;

/**
 * Register the routes required for your module here
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The root namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $namespace = 'Modules\CHTrips\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @param  Router $router
     * @return void
     */
    public function before(Router $router)
    {
        //
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function map(Router $router)
    {
        $this->registerWebRoutes();
        $this->registerAdminRoutes();
    }

    /**
     *
     */
    protected function registerWebRoutes(): void
    {
        $config = [
            'as'         => 'chtrips.',
            'namespace'  => $this->namespace.'\Frontend',
            'middleware' => ['web'],
        ];

        Route::group($config, function () {
            Route::group(['middleware' => 'auth', 'prefix' => 'trips'], function () {
                Route::get('/', 'IndexController@index')->name('index');
                Route::get('/create', 'IndexController@create')->name('create');
                Route::post('/', 'IndexController@store')->name('store');
                Route::get('/{trip}', [IndexController::class, 'show'])->name('show');
            });
        });
    }

    protected function registerAdminRoutes(): void
    {
        $config = [
            'as'         => 'admin.',
            'prefix'     => 'admin/',
            'namespace'  => $this->namespace.'\Admin',
            'middleware' => ['web', 'role:admin'],
        ];

        Route::group($config, function () {
            Route::group(['as' => 'trips.', 'prefix' => 'trips/'], function () {
                Route::get('/', [TripsAdminController::class, 'index']);
                Route::get('create', [TripsAdminController::class, 'create']);
                Route::post('/', [TripsAdminController::class, 'store']);
                Route::group(['prefix' => '/templates', 'as' => 'templates.'], function () {
                    Route::get('/', [TripTemplatesAdminController::class, 'index']);
                    Route::post('/', [TripTemplatesAdminController::class, 'store']);
                    Route::get('/create', [TripTemplatesAdminController::class, 'create']);
                    Route::get('/{id}', [TripTemplatesAdminController::class, 'show']);
                    Route::get('/{id}/edit', [TripTemplatesAdminController::class, 'edit']);
                    Route::patch('/{id}', [TripTemplatesAdminController::class, 'update']);
                    Route::delete('/{id}', [TripTemplatesAdminController::class, 'destroy']);
                });
                Route::get('/{id}', [TripsAdminController::class, 'show']);
                Route::get('/{id}/edit', [TripsAdminController::class, 'edit']);
            });
            Route::group(['as' => 'missions.', 'prefix' => 'missions/'], function () {

            });
        });
    }
}
