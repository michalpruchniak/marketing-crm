<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::LeadsView->value);
    }

    public function view(User $user, Lead $lead): bool
    {
        if (! $user->can(Permission::LeadsView->value)) {
            return false;
        }

        if ($user->can(Permission::LeadsUpdateAny->value)) {
            return true;
        }

        return $this->isOwner($user, $lead);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::LeadsCreate->value);
    }

    public function update(User $user, Lead $lead): bool
    {
        if ($user->can(Permission::LeadsUpdateAny->value)) {
            return true;
        }

        return $user->can(Permission::LeadsUpdateOwn->value)
            && $this->isOwner($user, $lead);
    }

    private function isOwner(User $user, Lead $lead): bool
    {
        return (int) $lead->sales_id === (int) $user->id;
    }
}
