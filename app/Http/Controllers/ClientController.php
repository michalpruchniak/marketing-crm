<?php

namespace App\Http\Controllers;

use App\Http\Requests\Clients\StoreClientRequest;
use App\Http\Requests\Clients\UpdateClientRequest;
use App\Models\Client;
use App\Services\Contracts\ClientServiceInterface;
use App\Services\Contracts\CredentialServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly ClientServiceInterface $clientService,
        private readonly CredentialServiceInterface $credentialService,
    ) {}

    public function index(): Response
    {
        $this->authorize('viewAny', Client::class);

        $clients = $this->clientService->getAll();

        return Inertia::render('clients/index', [
            'clients' => $clients,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Client::class);

        return Inertia::render('clients/create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $this->authorize('create', Client::class);

        $client = $this->clientService->create($request->getDTO());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Client created.'),
        ]);

        return to_route('clients.show', $client);
    }

    public function show(Client $client): Response
    {
        $this->authorize('view', $client);

        $client->load(['coordinator:id,name']);
        $credentials = $this->credentialService->allForClient($client->id);

        return Inertia::render('clients/show', [
            'client' => $client,
            'credentials' => $credentials,
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $this->clientService->update($client, $request->getDTO());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Client updated.'),
        ]);

        return to_route('clients.show', $client);
    }

    public function destroy(Client $client): RedirectResponse
    {
        $this->authorize('delete', $client);

        $this->clientService->delete($client);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Client deleted.'),
        ]);

        return to_route('clients.index');
    }
}
