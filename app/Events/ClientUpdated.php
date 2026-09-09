<?php

namespace App\Events;

use App\Models\Client;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Client $client)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('clients'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'client.updated';
    }

    public function broadcastWith(): array
    {
        $coordinator = $this->client->coordinator;

        return [
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'email' => $this->client->email,
                'phone' => $this->client->phone,
                'notes' => $this->client->notes,
                'coordinator_id' => $this->client->coordinator_id,
                'coordinator' => $coordinator === null ? null : [
                    'id' => $coordinator->id,
                    'name' => $coordinator->name,
                ],
            ],
        ];


    }
}
