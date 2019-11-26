<?php

namespace App\Providers;

use App\Events\DebtLogSaved;
use App\Events\DebtsSynced;
use App\Events\GotMessagesAllowed;
use App\Events\GotMessagesDenied;
use App\Events\GotNewMessage;
use App\Events\UserCreated;
use App\Listeners\CalcDebtValues;
use App\Listeners\CalcDebtValuesForSyncedDebtors;
use App\Listeners\EnableMessages;
use App\Listeners\FillPersonalDataFromVk;
use App\Listeners\ParseIncomeMessage;
use App\Listeners\SendNotificationToSyncedDebt;
use App\Listeners\SetMessagesAllowed;
use App\Listeners\SetMessagesDenied;
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
            CalcDebtValues::class,
            SendNotificationToSyncedDebt::class
        ],

        GotMessagesAllowed::class => [
            SetMessagesAllowed::class
        ],
        GotMessagesDenied::class => [
            SetMessagesDenied::class
        ],

        DebtsSynced::class => [
            CalcDebtValuesForSyncedDebtors::class
        ]
    ];
}
