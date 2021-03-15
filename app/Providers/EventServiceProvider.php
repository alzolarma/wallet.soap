<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use App\Events\TransactionCreated;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Listeners\UpdateBalance;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        TransactionCreated::class => [
            UpdateBalance::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Event::listen('event.*', function ($eventName, array $data) {
        //     //
        // });
    }
}
