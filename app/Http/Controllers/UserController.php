<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly UserServiceInterface $userService,
    ) {}

    public function index(): Response
    {
        $this->authorize('viewAny', User::class);

        $users = $this->userService->getAll()->map(static fn (User $user): array => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'banned_at' => $user->banned_at,
            'roles' => $user->roles->pluck('name')->values()->all(),
            'created_at' => $user->created_at,
        ]);

        return Inertia::render('users/index', [
            'users' => $users,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('users/create');
    }

    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        $user->load('roles:id,name');

        return Inertia::render('users/edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->first() ?? '',
            ],
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->create($request->getDTO());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User created.'),
        ]);

        return to_route('users.index');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->update($user, $request->getDTO());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User updated.'),
        ]);

        return to_route('users.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->userService->delete($user);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User deleted.'),
        ]);

        return to_route('users.index');
    }

    public function ban(User $user): RedirectResponse
    {
        $this->authorize('ban', $user);

        $this->userService->ban($user);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User banned.'),
        ]);

        return to_route('users.index');
    }

    public function unban(User $user): RedirectResponse
    {
        $this->authorize('ban', $user);

        $this->userService->unban($user);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User unbanned.'),
        ]);

        return to_route('users.index');
    }
}
