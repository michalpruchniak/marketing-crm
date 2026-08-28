<?php

namespace App\Http\Middleware;

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'can' => $this->permissions($request),
            ...$this->coordinators($request),
        ];
    }

    private function permissions(Request $request): array
    {
        $user = $request->user();
        $can = [];

        if ($request->routeIs('clients.index')) {
            $can['canClientsCreate'] =
                $user?->can(Permission::ClientsCreate->value) ?? false;
        }

        if ($request->routeIs('clients.show')) {
            $can = [
                'canClientsCreate' =>
                    $user?->can(Permission::ClientsCreate->value) ?? false,

                'canClientsAssignCoordinator' =>
                    $user?->can(Permission::ClientsAssignCoordinator->value) ?? false,

                'canCredentialsView' =>
                    $user?->can(Permission::CredentialsView->value) ?? false,

                'canCredentialsCreate' =>
                    $user?->can(Permission::CredentialsCreate->value) ?? false,

                'canCredentialsReveal' =>
                    $user?->can(Permission::CredentialsReveal->value) ?? false,

                'canCredentialsDelete' =>
                    $user?->can(Permission::CredentialsDelete->value) ?? false,
            ];
        }

        return $can;
    }


    /**
     * @return array{coordinators: array<int, array{id: int, name: string}>}|array{}
     */
    private function coordinators(Request $request): array
    {
        if (! $request->routeIs(['clients.create', 'clients.show'])) {
            return [];
        }

        $user = $request->user();

        if (! ($user?->can(Permission::ClientsAssignCoordinator->value) ?? false)) {
            return [];
        }

        return [
            'coordinators' => User::query()
                ->assignableCoordinators()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(static fn (User $coordinator): array => [
                    'id' => $coordinator->id,
                    'name' => $coordinator->name,
                ])
                ->values()
                ->all(),
        ];
    }
}
