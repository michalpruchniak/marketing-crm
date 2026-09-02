<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Requests\Leads\StoreLeadRequest;
use App\Http\Requests\Leads\UpdateLeadLabelRequest;
use App\Http\Requests\Leads\UpdateLeadRequest;
use App\Models\Lead;
use App\Models\User;
use App\Services\Contracts\LeadServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeadController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly LeadServiceInterface $leadService,
    ) {}

    public function index(Request $request): Response|RedirectResponse
    {
        $this->authorize('viewAny', Lead::class);

        /** @var User $user */
        $user = $request->user();

        $canViewAll = $user->can(Permission::LeadsUpdateAny->value);

        if (! $canViewAll && ! $request->has('mine')) {
            return redirect()->route('leads.index', ['mine' => 1]);
        }

        $onlyMine = $canViewAll ? $request->boolean('mine') : true;

        return Inertia::render('leads/index', [
            'leads' => $this->leadService->getAllForUser($user, $onlyMine),
            'filters' => [
                'mine' => $onlyMine,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Lead::class);

        return Inertia::render('leads/create');
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->leadService->create($request->getDTO(), $user);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Lead created.'),
        ]);

        return to_route('leads.index', $this->indexQueryForUser($user));
    }

    public function edit(Lead $lead): Response
    {
        $this->authorize('update', $lead);

        return Inertia::render('leads/edit', [
            'lead' => $lead,
        ]);
    }

    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->leadService->update($lead, $request->getDTO());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Lead updated.'),
        ]);

        return to_route('leads.index', $this->indexQueryForUser($user));
    }

    public function updateLabel(UpdateLeadLabelRequest $request, Lead $lead): RedirectResponse
    {
        $this->leadService->updateLabel($lead, $request->getLabel());

        return back();
    }

    /**
     * @return array{mine?: int}
     */
    private function indexQueryForUser(User $user): array
    {
        if ($user->can(Permission::LeadsUpdateAny->value)) {
            return [];
        }

        return ['mine' => 1];
    }
}
