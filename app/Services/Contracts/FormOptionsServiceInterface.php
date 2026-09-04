<?php

namespace App\Services\Contracts;

use App\Models\User;

interface FormOptionsServiceInterface
{

    /**
     * @return list<array{id: int, name: string}>
     */
    public function salesPersons(User $user): array;

    /**
     * @return list<array{value: string, label: string}>
     */
    public function roles(): array;

    /**
     * @return list<array{id: int, name: string}>
     */
    public function coordinators(): array;
}
