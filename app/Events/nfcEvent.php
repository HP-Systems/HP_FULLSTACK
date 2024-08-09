<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class nfcEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    public $user;


    public function __construct($message,$user)
    {
        
        $this->message = $message;
        $this->user = $user;
        Log::info('NFCEvent constructed with message: ' . $message);
    }

    public function broadcastOn()
    {
        Log::info('NFCEvent broadcast on channel: nfc-channel');
        return new Channel('nfc-channel');

    }
    public function broadcastAs()
    {
        return 'nfc-event';
    }

}
