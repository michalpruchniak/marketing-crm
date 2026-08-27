<?php

namespace App\Rules;

use App\Enums\Permission;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AssignableCoordinator implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $canAssign = auth()->user()?->can(Permission::ClientsAssignCoordinator->value) ?? false;

        if (! $canAssign) {
            return;
        }

        if ($value === null || $value === '') {
            $fail(__('validation.required', ['attribute' => $attribute]));

            return;
        }

        if (! is_numeric($value)) {
            $fail(__('The selected coordinator is invalid.'));

            return;
        }

        $isAssignable = User::query()
            ->assignableCoordinators()
            ->whereKey($value)
            ->exists();

        if (! $isAssignable) {
            $fail(__('The selected coordinator is invalid.'));
        }
    }
}
