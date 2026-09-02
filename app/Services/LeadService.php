<?php

namespace App\Services;

use App\Enums\LeadLabel;
use App\Enums\Permission;
use App\Http\DTO\StoreLeadDTO;
use App\Http\DTO\UpdateLeadDTO;
use App\Models\Lead;
use App\Models\User;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Services\Contracts\LeadServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use LogicException;

class LeadService implements LeadServiceInterface
{
    public function __construct(
        private readonly LeadRepositoryInterface $leadsRepository,
    ) {}

    /**
     * @return Collection<int, Lead>
     */
    public function getAllForUser(User $user, bool $onlyMine = false): Collection
    {
        $where = [];

        if ($onlyMine) {
            $where['sales_id'] = $user->id;
        }

        $leads = $this->leadsRepository->get(
            where: $where,
            orderBy: ['created_at' => 'desc'],
            columns: ['id', 'name', 'email', 'phone', 'label', 'sales_id', 'created_at'],
        );

        $leads->load(['salesPerson:id,name']);

        /** @var Collection<int, Lead> */
        return $leads;
    }

    public function create(StoreLeadDTO $dto, User $creator): Lead
    {
        $salesId = $dto->salesId;

        if (! $creator->can(Permission::LeadsUpdateAny->value)) {
            $salesId = $creator->id;
        }

        $lead = $this->leadsRepository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'notes' => $dto->notes,
            'label' => $dto->label->value,
            'sales_id' => $salesId,
        ]);

        if (! $lead instanceof Lead) {
            throw new LogicException('Expected Lead model instance.');
        }

        return $lead->load(['salesPerson:id,name']);
    }

    public function update(Lead $lead, UpdateLeadDTO $dto): Lead
    {
        $data = $dto->toArray();

        $user = Auth::user();

        if ($user instanceof User && $user->can(Permission::LeadsUpdateAny->value)) {
            $data['sales_id'] = $dto->salesId;
        }

        $lead = $this->leadsRepository->update($lead, $data);

        if (! $lead instanceof Lead) {
            throw new LogicException('Expected Lead model instance.');
        }

        return $lead->load(['salesPerson:id,name']);
    }

    public function updateLabel(Lead $lead, LeadLabel $label): Lead
    {
        $lead = $this->leadsRepository->update($lead, [
            'label' => $label->value,
        ]);

        if (! $lead instanceof Lead) {
            throw new LogicException('Expected Lead model instance.');
        }

        return $lead->load(['salesPerson:id,name']);
    }
}
