<?php

namespace App\Http\Middleware;

use App\Enums\LeadLabel;
use App\Enums\Permission;
use App\Models\User;
use App\Services\Contracts\FormOptionsServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class FormOptionsMiddleware
{
    public function __construct(
        private readonly FormOptionsServiceInterface $formOptions,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $shares = [];
        $user = $request->user();

        if ($request->routeIs(['leads.index', 'leads.create', 'leads.edit'])) {
            $shares['leadLabels'] = LeadLabel::values();
        }

        if ($request->routeIs(['leads.create', 'leads.edit']) && $user instanceof User) {
            $shares['salesPersons'] = $this->formOptions->salesPersons($user);
        }

        if ($request->routeIs(['users.create', 'users.edit'])
            && $user instanceof User
            && ($user->can(Permission::UsersCreate->value) || $user->can(Permission::UsersUpdate->value))) {
            $shares['roles'] = $this->formOptions->roles();
        }

        if ($request->routeIs(['clients.create', 'clients.show'])
            && $user instanceof User
            && $user->can(Permission::ClientsAssignCoordinator->value)) {
            $shares['coordinators'] = $this->formOptions->coordinators();
        }

        if ($shares !== []) {
            Inertia::share($shares);
        }

        return $next($request);
    }
}
