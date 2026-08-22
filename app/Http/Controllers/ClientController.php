<?php

namespace App\Http\Controllers;

use App\Http\Requests\Clients\StoreClientRequest;
use App\Models\Client;
use App\Services\Contracts\ClientServiceInterface;
use App\Services\Contracts\CredentialServiceInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
class ClientController extends Controller
{
    public function __construct(
        private readonly ClientServiceInterface $clientService,
        private readonly CredentialServiceInterface $credentialService,
    ) {}

    public function index(): Response
    {
        $clients = $this->clientService->getAll();

        return Inertia::render('clients/index', [
            'clients' => $clients,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('clients/create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $client = $this->clientService->create($request->getDTO());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Client created.'),
        ]);

        return to_route('clients.show', $client);
    }

    public function show(Client $client): Response
    {
        $credentials = $this->credentialService->allForClient($client->id);

        return Inertia::render('clients/show', [
            'client' => $client,
            'credentials' => $credentials
        ]);
    }

    public function destroy(Client $client): RedirectResponse
    {
        $this->clientService->delete($client);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Client deleted.'),
        ]);

        return to_route('clients.index');
    }
}
