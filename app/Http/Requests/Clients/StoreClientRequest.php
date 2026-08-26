<?php

namespace App\Http\Requests\Clients;

use App\Http\DTO\StoreClientDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function getDTO(): StoreClientDTO
    {
        return new StoreClientDTO(
            name: $this->validated('name'),
            email: $this->validated('email'),
            phone: $this->validated('phone'),
            notes: $this->validated('notes'),
        );
    }
}
