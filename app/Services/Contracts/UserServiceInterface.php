<?php

namespace App\Services\Contracts;

use App\Http\DTO\StoreUserDTO;
use App\Http\DTO\UpdateUserDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface
{
    /**
     * @return Collection<int, User>
     */
    public function getAll(): Collection;

    /**
     * @param  StoreUserDTO  $dto
     * @return User
     */
    public function create(StoreUserDTO $dto): User;

    /**
     * @param  User  $user
     * @param  UpdateUserDTO  $dto
     * @return User
     */
    public function update(User $user, UpdateUserDTO $dto): User;

    /**
     * @param  User  $user
     * @return void
     */
    public function delete(User $user): void;

    /**
     * @param  User  $user
     * @return User
     */
    public function ban(User $user): User;

    /**
     * @param  User  $user
     * @return User
     */
    public function unban(User $user): User;
}
