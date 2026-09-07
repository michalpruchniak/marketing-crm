<?php

namespace App\Rules;

use App\Enums\Role;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AssignableSalesPerson implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! is_numeric($value)) {
            $fail(__('The selected sales person is invalid.'));

            return;
        }

        $isAssignable = User::query()
            ->role(Role::Sales->value)
            ->whereKey($value)
            ->exists();

        if (! $isAssignable) {
            $fail(__('The selected sales person is invalid.'));
        }
    }
}
