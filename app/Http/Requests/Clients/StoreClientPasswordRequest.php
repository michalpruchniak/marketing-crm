<?php

namespace App\Http\Requests\Clients;

use App\Http\DTO\StoreCredentialDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientPasswordRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:2000'],
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:1000'],
            'additional_information' => ['nullable', 'string', 'max:2000'],
            'url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    public function getDTO(): StoreCredentialDTO
    {
        return new StoreCredentialDTO(
            clientId: $this->route('client')->id,
            userId: (int) $this->user()->id,
            name: $this->validated('name'),
            description: $this->validated('description'),
            login: $this->validated('login'),
            password: $this->validated('password'),
            additionalInformation: $this->validated('additional_information'),
            url: $this->validated('url'),
        );
    }
}
