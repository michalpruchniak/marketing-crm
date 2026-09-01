<?php

namespace App\Http\Requests\Users;

use App\Enums\Role;
use App\Http\DTO\UpdateUserDTO;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User $user */
        $user = $this->route('user');

        return $this->user()?->can('update', $user) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', Rule::in(Role::values())],
        ];
    }

    public function getDTO(): UpdateUserDTO
    {
        return new UpdateUserDTO(
            name: $this->validated('name'),
            email: $this->validated('email'),
            password: $this->validated('password'),
            role: $this->validated('role'),
        );
    }
}
