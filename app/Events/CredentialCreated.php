<?php

namespace App\Events;

use App\Models\Credential;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CredentialCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Credential $credential) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('clients.'.$this->credential->client_id.'.credentials'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'credential.created';
    }

    /**
     * @return array{credential: array{id: string, name: string, description: string|null, type: string}}
     */
    public function broadcastWith(): array
    {
        return [
            'credential' => [
                'id' => $this->credential->id,
                'name' => $this->credential->name,
                'description' => $this->credential->description,
                'type' => $this->credential->type->value,
            ],
        ];
    }
}
