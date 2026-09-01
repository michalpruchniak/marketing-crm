<?php

namespace App\Http\Requests\Users;

use App\Enums\Role;
use App\Http\DTO\StoreUserDTO;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', Rule::in(Role::values())],
        ];
    }

    public function getDTO(): StoreUserDTO
    {
        return new StoreUserDTO(
            name: $this->validated('name'),
            email: $this->validated('email'),
            password: $this->validated('password'),
            role: $this->validated('role'),
        );
    }
}
