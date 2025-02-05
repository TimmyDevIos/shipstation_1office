<?php

namespace App\Events\_1Office_KiotViet\Customer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Customer;

class CustomerDeleted
{
    use Dispatchable, SerializesModels;

    public $customerInfo;

    public function __construct(array $customerInfo)
    {
        $this->customerInfo = $customerInfo;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
