<?php

namespace App\Http\Requests\Clients;

use App\Enums\Permission;
use App\Http\DTO\UpdateClientDTO;
use App\Models\Client;
use App\Rules\AssignableCoordinator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Client $client */
        $client = $this->route('client');

        return $this->user()?->can('update', $client) ?? false;
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
            'coordinator_id' => [new AssignableCoordinator],
        ];
    }

    public function getDTO(): UpdateClientDTO
    {
        $canAssignCoordinator = $this->user()?->can(Permission::ClientsAssignCoordinator->value) ?? false;

        return new UpdateClientDTO(
            name: $this->validated('name'),
            email: $this->validated('email'),
            phone: $this->validated('phone'),
            notes: $this->validated('notes'),
            coordinatorId: $canAssignCoordinator
                ? (int) $this->validated('coordinator_id')
                : null,
        );
    }
}
