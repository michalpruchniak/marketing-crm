<?php

namespace App\Services;

use App\Http\DTO\StoreUserDTO;
use App\Http\DTO\UpdateUserDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use LogicException;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $usersRepository,
    ) {}

    /**
     * @return Collection<int, User>
     */
    public function getAll(): Collection
    {
        $users = $this->usersRepository->get(
            orderBy: ['name' => 'asc'],
            columns: ['id', 'name', 'email', 'banned_at', 'created_at'],
        );

        $users->load('roles:id,name');

        /** @var Collection<int, User> */
        return $users;
    }

    public function create(StoreUserDTO $dto): User
    {
        return DB::transaction(function () use ($dto): User {
            $user = $this->usersRepository->create($dto->toArray());

            if (! $user instanceof User) {
                throw new LogicException('Expected User model instance.');
            }

            $user->syncRoles([$dto->role]);

            return $user->load('roles:id,name');
        });
    }

    public function update(User $user, UpdateUserDTO $dto): User
    {
        return DB::transaction(function () use ($user, $dto): User {
            $user = $this->usersRepository->update($user, $dto->toArray());

            if (! $user instanceof User) {
                throw new LogicException('Expected User model instance.');
            }

            $user->syncRoles([$dto->role]);

            return $user->load('roles:id,name');
        });
    }

    public function delete(User $user): void
    {
        $this->usersRepository->delete($user);
    }

    public function ban(User $user): User
    {
        $user = $this->usersRepository->update($user, [
            'banned_at' => now(),
        ]);

        if (! $user instanceof User) {
            throw new LogicException('Expected User model instance.');
        }

        return $user->load('roles:id,name');
    }

    public function unban(User $user): User
    {
        $user = $this->usersRepository->update($user, [
            'banned_at' => null,
        ]);

        if (! $user instanceof User) {
            throw new LogicException('Expected User model instance.');
        }

        return $user->load('roles:id,name');
    }
}
