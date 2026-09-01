<?php

namespace App\Http\DTO;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string>
 */
final readonly class UpdateUserDTO implements Arrayable
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
        public string $role,
    ) {}

    /**
     * @return array{name: string, email: string, password?: string}
     */
    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password !== null) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}
