<?php

namespace App\Http\DTO;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string|int|null>
 */
final readonly class UpdateClientDTO implements Arrayable
{
    /**
     * @param  string  $name
     * @param  string|null  $email
     * @param  string|null  $phone
     * @param  string|null  $notes
     * @param  int|null  $coordinatorId
     */
    public function __construct(
        public string $name,
        public ?string $email,
        public ?string $phone,
        public ?string $notes,
        public ?int $coordinatorId = null,
    ) {}

    /**
     * @return array{name: string, email: string|null, phone: string|null, notes: string|null, coordinator_id?: int}
     */
    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'coordinator_id' => $this->coordinatorId ?? null,
        ];

        return $data;
    }
}
