<?php

namespace App\Http\Requests\Clients;

use App\Http\DTO\StoreCredentialDTO;
use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use LogicException;

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
            'login' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:1000'],
            'additional_information' => ['nullable', 'string', 'max:2000'],
            'url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    public function getDTO(): StoreCredentialDTO
    {
        $client = $this->route('client');

        if (! $client instanceof Client) {
            throw new LogicException('The route is missing a bound client.');
        }

        return new StoreCredentialDTO(
            clientId: $client->id,
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
