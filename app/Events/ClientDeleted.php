<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public string $clientId) {}

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
        return 'client.deleted';
    }

    /**
     * @return array{client: array{id: string}}
     */
    public function broadcastWith(): array
    {
        return [
            'client' => [
                'id' => $this->clientId,
            ],
        ];
    }
}
