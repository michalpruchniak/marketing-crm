<?php

namespace App\Http\DTO;

use App\Enums\LeadLabel;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string|int|null>
 */
final readonly class StoreLeadDTO implements Arrayable
{
    public function __construct(
        public string $name,
        public ?string $email,
        public ?string $phone,
        public ?string $notes,
        public LeadLabel $label,
        public ?int $salesId = null,
    ) {}

    /**
     * @return array{name: string, email: string|null, phone: string|null, notes: string|null, label: string, sales_id: int|null}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'label' => $this->label->value,
            'sales_id' => $this->salesId,
        ];
    }
}
