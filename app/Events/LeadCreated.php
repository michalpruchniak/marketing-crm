<?php

namespace App\Events;

use App\Models\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Lead $lead) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('leads'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'lead.created';
    }

    /**
     * @return array{lead: array{id: int, name: string, email: string|null, phone: string|null, notes: string|null, label: string, sales_id: int|null, created_at: string|null, sales_person: array{id: int, name: string}|null}}
     */
    public function broadcastWith(): array
    {
        $salesPerson = $this->lead->salesPerson;

        return [
            'lead' => [
                'id' => $this->lead->id,
                'name' => $this->lead->name,
                'email' => $this->lead->email,
                'phone' => $this->lead->phone,
                'notes' => $this->lead->notes,
                'label' => $this->lead->label->value,
                'sales_id' => $this->lead->sales_id,
                'created_at' => $this->lead->created_at?->toISOString(),
                'sales_person' => $salesPerson === null ? null : [
                    'id' => $salesPerson->id,
                    'name' => $salesPerson->name,
                ],
            ],
        ];
    }
}
