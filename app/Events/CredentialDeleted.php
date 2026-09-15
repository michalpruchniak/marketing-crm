<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CredentialDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $clientId,
        public string $credentialId,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('clients.'.$this->clientId.'.credentials'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'credential.deleted';
    }

    /**
     * @return array{credential: array{id: string}}
     */
    public function broadcastWith(): array
    {
        return [
            'credential' => [
                'id' => $this->credentialId,
            ],
        ];
    }
}
