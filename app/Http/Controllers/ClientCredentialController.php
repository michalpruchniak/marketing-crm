<?php

namespace App\Http\Controllers;

use App\Http\Requests\Clients\StoreClientPasswordRequest;
use App\Models\Client;
use App\Services\Contracts\CredentialServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ClientCredentialController extends Controller
{
    public function __construct(
        private readonly CredentialServiceInterface $credentialService,
    ) {}

    public function store(StoreClientPasswordRequest $request, Client $client): RedirectResponse
    {
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
        try {
            $payload = $this->credentialService->reveal($client, $credential);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'id' => $payload->id,
            'name' => $payload->name,
            'description' => $payload->description,
            'login' => $payload->login,
            'password' => $payload->password,
            'additional_information' => $payload->additionalInformation,
        ]);
    }

    public function destroy(Client $client, string $credential): RedirectResponse
    {
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
