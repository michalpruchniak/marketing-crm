<?php

namespace App\Services\Contracts;

use App\Enums\LeadLabel;
use App\Http\DTO\StoreLeadDTO;
use App\Http\DTO\UpdateLeadDTO;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface LeadServiceInterface
{
    /**
     * @return Collection<int, Lead>
     */
    public function getAllForUser(User $user, bool $onlyMine = false): Collection;

    public function create(StoreLeadDTO $dto, User $creator): Lead;

    public function update(Lead $lead, UpdateLeadDTO $dto): Lead;

    public function updateLabel(Lead $lead, LeadLabel $label): Lead;
}
