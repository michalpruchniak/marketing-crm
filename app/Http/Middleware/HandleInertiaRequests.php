<?php

namespace App\Http\Middleware;

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
            ...$this->clientPermissions($request),
        ];
    }

    /**
     * @return array{can: array{update: bool, delete: bool}}|array{}
     */
    private function clientPermissions(Request $request): array
    {
        if (! $request->routeIs('clients.show')) {
            return [];
        }

        $client = $request->route('client');

        if (! $client instanceof Client) {
            return [];
        }

        $user = $request->user();

        return [
            'can' => [
                'update' => $user?->can('update', $client) ?? false,
                'delete' => $user?->can('delete', $client) ?? false,
            ],
        ];
    }
}
