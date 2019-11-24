<?php

namespace App\Providers;

use App\Events\DebtLogSaved;
use App\Events\GotNewMessage;
use App\Events\UserCreated;
use App\Listeners\CalcDebtValues;
use App\Listeners\EnableMessages;
use App\Listeners\FillPersonalDataFromVk;
use App\Listeners\ParseIncomeMessage;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserCreated::class => [
            FillPersonalDataFromVk::class
        ],
        GotNewMessage::class => [
            EnableMessages::class,
            ParseIncomeMessage::class
        ],
        DebtLogSaved::class => [
            CalcDebtValues::class
        ]
    ];
}
