<?php

namespace App\Http\Middleware;

use App\Enums\Permission;
use App\Models\Client;
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
        ];
    }

    /**
     * @return array<string, bool>
     */
    private function permissions(Request $request): array
    {
        $user = $request->user();

        $allPermissions = [
            'canClientsCreate' => $user?->can(Permission::ClientsCreate->value) ?? false,
            'canClientsAssignCoordinator' => $user?->can(Permission::ClientsAssignCoordinator->value) ?? false,
            'canCredentialsView' => $user?->can(Permission::CredentialsView->value) ?? false,
            'canCredentialsCreate' => $user?->can(Permission::CredentialsCreate->value) ?? false,
            'canCredentialsReveal' => $user?->can(Permission::CredentialsReveal->value) ?? false,
            'canCredentialsDelete' => $user?->can(Permission::CredentialsDelete->value) ?? false,
            'canUsersView' => $user?->can(Permission::UsersView->value) ?? false,
            'canUsersCreate' => $user?->can(Permission::UsersCreate->value) ?? false,
            'canUsersUpdate' => $user?->can(Permission::UsersUpdate->value) ?? false,
            'canUsersDelete' => $user?->can(Permission::UsersDelete->value) ?? false,
            'canUsersBan' => $user?->can(Permission::UsersBan->value) ?? false,
            'canLeadsView' => $user?->can(Permission::LeadsView->value) ?? false,
            'canLeadsCreate' => $user?->can(Permission::LeadsCreate->value) ?? false,
            'canLeadsUpdateAny' => $user?->can(Permission::LeadsUpdateAny->value) ?? false,
        ];

        if ($request->routeIs('clients.show')) {
            $client = $request->route('client');

            if ($client instanceof Client) {
                $allPermissions['canClientsUpdate'] = $user?->can('update', $client) ?? false;
                $allPermissions['canClientsDelete'] = $user?->can('delete', $client) ?? false;
            }
        }

        return $allPermissions;
    }
}
