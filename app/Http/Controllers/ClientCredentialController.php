<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Requests\Clients\StoreClientPasswordRequest;
use App\Models\Client;
use App\Services\Contracts\CredentialServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ClientCredentialController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly CredentialServiceInterface $credentialService,
    ) {}

    public function store(StoreClientPasswordRequest $request, Client $client): RedirectResponse
    {
        $this->authorize(Permission::CredentialsCreate->value);

        try {
            $this->credentialService->store($request->getDTO());
        } catch (Throwable $exception) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('The secret could not be saved: :error', [
                    'error' => $exception->getMessage(),
                ]),
            ]);

            return back()->withInput();
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Credential saved.'),
        ]);

        return to_route('clients.show', $client);
    }

    public function reveal(Client $client, string $credential): JsonResponse
    {
        $this->authorize(Permission::CredentialsReveal->value);

        try {
            $payload = $this->credentialService->reveal($client, $credential);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($payload->toArray());
    }

    public function destroy(Client $client, string $credential): RedirectResponse
    {
        $this->authorize(Permission::CredentialsDelete->value);

        try {
            $this->credentialService->delete($client, $credential);
        } catch (Throwable $exception) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('The secret could not be deleted: :error', [
                    'error' => $exception->getMessage(),
                ]),
            ]);

            return back();
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Credential deleted.'),
        ]);

        return to_route('clients.show', $client);
    }
}
