<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\BroadcastsUsing;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StoreOwnerRegistered
{
    use Dispatchable;

    public function __construct(
        public User $user
    ) {}
}
