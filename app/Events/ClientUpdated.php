<?php

namespace App\Events;

use App\Models\Client;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Client $client) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('clients'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'client.updated.single';
    }

    /**
     * @return array{client: array{id: string, name: string, email: string|null, phone: string|null, notes: string|null, coordinator_id: int|null, coordinator: array{id: int, name: string}|null}}
     */
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
