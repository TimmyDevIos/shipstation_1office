<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

// Import các class Cho 1Office_KiotViet
use App\Events\_1Office_KiotViet\Customer\CustomerCreated as _1Office_KiotVietCustomerCreated;
use App\Events\_1Office_KiotViet\Customer\CustomerUpdated as _1Office_KiotVietCustomerUpdated;
use App\Events\_1Office_KiotViet\Customer\CustomerDeleted as _1Office_KiotVietCustomerDeleted;
use App\Listeners\_1Office_KiotViet\Customer\SyncCustomerCreate as _1Office_KiotVietSyncCustomerCreate;
use App\Listeners\_1Office_KiotViet\Customer\SyncCustomerUpdate as _1Office_KiotVietSyncCustomerUpdate;
use App\Listeners\_1Office_KiotViet\Customer\SyncCustomerDelete as _1Office_KiotVietSyncCustomerDelete;

// Import các class Cho 1Office_ShipStation
use App\Events\_1Office_ShipStation\Order\OrderCreated as _1Office_ShipStationOrderCreated;
use App\Events\_1Office_ShipStation\Order\OrderUpdated as _1Office_ShipStationOrderUpdated;
use App\Events\_1Office_ShipStation\Order\OrderDeleted as _1Office_ShipStationOrderDeleted;
use App\Listeners\_1Office_ShipStation\Order\SyncOrderCreate as _1Office_ShipStationSyncOrderCreate;
use App\Listeners\_1Office_ShipStation\Order\SyncOrderUpdate as _1Office_ShipStationSyncOrderUpdate;
use App\Listeners\_1Office_ShipStation\Order\SyncOrderDelete as _1Office_ShipStationSyncOrderDelete;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        // Định nghĩa cho 1Office_KiotViet namespace
        _1Office_KiotVietCustomerCreated::class => [
            _1Office_KiotVietSyncCustomerCreate::class,
        ],
        _1Office_KiotVietCustomerUpdated::class => [
            _1Office_KiotVietSyncCustomerUpdate::class,
        ],
        _1Office_KiotVietCustomerDeleted::class => [
            _1Office_KiotVietSyncCustomerDelete::class,
        ],
        
        // Định nghĩa cho 1Office_ShipStation namespace
        _1Office_ShipStationOrderCreated::class => [
            _1Office_ShipStationSyncOrderCreate::class,
        ],
        _1Office_ShipStationOrderUpdated::class => [
            _1Office_ShipStationSyncOrderUpdate::class,
        ],
        _1Office_ShipStationOrderDeleted::class => [
            _1Office_ShipStationSyncOrderDelete::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
