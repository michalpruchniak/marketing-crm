<?php

namespace App\Http\Requests\Leads;

use App\Enums\LeadLabel;
use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadLabelRequest extends FormRequest
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
        return [
            'label' => ['required', 'string', Rule::in(LeadLabel::values())],
        ];
    }

    public function getLabel(): LeadLabel
    {
        return LeadLabel::from($this->validated('label'));
    }
}
