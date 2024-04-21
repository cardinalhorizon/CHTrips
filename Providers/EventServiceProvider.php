<?php

namespace Modules\CHTrips\Providers;

use App\Events\PirepAccepted;
use App\Events\PirepFiled;
use App\Events\PirepPrefiled;
use App\Events\TestEvent;
use Modules\CHTrips\Listeners\PirepAcceptedListener;
use Modules\CHTrips\Listeners\PirepFiledListener;
use Modules\CHTrips\Listeners\PirepPrefiledListener;
use Modules\CHTrips\Listeners\TestEventListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     */
    protected $listen = [
        PirepPrefiled::class => [PirepPrefiledListener::class],
        PirepFiled::class    => [PirepFiledListener::class],
        PirepAccepted::class => [PirepAcceptedListener::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
    }
}
