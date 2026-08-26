<?php

namespace App\Http\DTO;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string|null>
 */
final readonly class UpdateClientDTO implements Arrayable
{
    public function __construct(
        public string $name,
        public ?string $email,
        public ?string $phone,
        public ?string $notes,
    ) {}

    /**
     * @return array{name: string, email: string|null, phone: string|null, notes: string|null}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
        ];
    }
}
