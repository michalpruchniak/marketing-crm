<?php

namespace App\Services;

use App\Enums\LeadLabel;
use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use App\Services\Contracts\FormOptionsServiceInterface;

class FormOptionsService implements FormOptionsServiceInterface
{
    /**
     * @return list<array{value: string}>
     */
    public function leadLabels(): array
    {
        return array_map(
            static fn (LeadLabel $label): array => ['value' => $label->value],
            LeadLabel::cases(),
        );
    }

    /**
     * @return list<array{id: int, name: string}>
     */
    public function salesPersons(User $user): array
    {
        if (! $user->can(Permission::LeadsUpdateAny->value)) {
            return [];
        }

        return User::query()
            ->role(Role::Sales->value)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(static fn (User $salesPerson): array => [
                'id' => $salesPerson->id,
                'name' => $salesPerson->name,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public function roles(): array
    {
        return array_map(
            static fn (Role $role): array => [
                'value' => $role->value,
                'label' => ucfirst($role->value),
            ],
            Role::cases(),
        );
    }

    /**
     * @return list<array{id: int, name: string}>
     */
    public function coordinators(): array
    {
        return User::query()
            ->assignableCoordinators()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(static fn (User $coordinator): array => [
                'id' => $coordinator->id,
                'name' => $coordinator->name,
            ])
            ->values()
            ->all();
    }
}
