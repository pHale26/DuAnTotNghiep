<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeatAvailabilityUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $scheduleId;

    public $availableSeats;

    /**
     * Create a new event instance.
     */
    public function __construct($scheduleId, $availableSeats)
    {
        $this->scheduleId = $scheduleId;
        $this->availableSeats = $availableSeats;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('tour-schedule.'.$this->scheduleId),
        ];
    }
}
