<?php

namespace App\Http\Requests\Leads;

use App\Enums\LeadLabel;
use App\Enums\Permission;
use App\Http\DTO\UpdateLeadDTO;
use App\Models\Lead;
use App\Rules\AssignableSalesPerson;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Lead $lead */
        $lead = $this->route('lead');

        return $this->user()?->can('update', $lead) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'label' => ['required', 'string', Rule::in(LeadLabel::values())],
        ];

        if ($this->user()?->can(Permission::LeadsUpdateAny->value) ?? false) {
            $rules['sales_id'] = ['nullable', new AssignableSalesPerson];
        }

        return $rules;
    }

    public function getDTO(): UpdateLeadDTO
    {
        $salesId = $this->user()?->can(Permission::LeadsUpdateAny->value) ?? false
            ? ($this->filled('sales_id') ? (int) $this->validated('sales_id') : null)
            : null;

        return new UpdateLeadDTO(
            name: $this->validated('name'),
            email: $this->validated('email'),
            phone: $this->validated('phone'),
            notes: $this->validated('notes'),
            label: LeadLabel::from($this->validated('label')),
            salesId: $salesId,
        );
    }
}
