<?php

use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (PermissionEnum::values() as $permission) {
            Permission::findOrCreate($permission);
        }

        /**
         * Add new permissions to the relevant role arrays below.
         * Admin always receives every permission from PermissionEnum::values().
         *
         * @var array<string, list<string>>
         */
        $rolePermissions = [
            RoleEnum::Admin->value => PermissionEnum::values(),

            RoleEnum::Manager->value => [
                PermissionEnum::ClientsView->value,
                PermissionEnum::ClientsCreate->value,
                PermissionEnum::ClientsUpdateOwn->value,
                PermissionEnum::ClientsUpdateAny->value,
                PermissionEnum::ClientsDeleteOwn->value,
                PermissionEnum::ClientsAssignCoordinator->value,
                PermissionEnum::CredentialsView->value,
                PermissionEnum::CredentialsCreate->value,
                PermissionEnum::CredentialsReveal->value,
                PermissionEnum::CredentialsDelete->value,
            ],

            RoleEnum::Coordinator->value => [
                PermissionEnum::ClientsView->value,
                PermissionEnum::ClientsCreate->value,
                PermissionEnum::ClientsUpdateOwn->value,
                PermissionEnum::ClientsDeleteOwn->value,
                PermissionEnum::CredentialsView->value,
                PermissionEnum::CredentialsCreate->value,
                PermissionEnum::CredentialsReveal->value,
                PermissionEnum::CredentialsDelete->value,
            ],

            RoleEnum::Developer->value => [
                PermissionEnum::ClientsView->value,
                PermissionEnum::CredentialsView->value,
                PermissionEnum::CredentialsCreate->value,
                PermissionEnum::CredentialsReveal->value,
                PermissionEnum::CredentialsDelete->value,
            ],

            RoleEnum::Sales->value => [
                PermissionEnum::ClientsView->value,
                PermissionEnum::CredentialsView->value,
                PermissionEnum::CredentialsCreate->value,
                PermissionEnum::CredentialsReveal->value,
                PermissionEnum::CredentialsDelete->value,
            ],

            RoleEnum::Marketer->value => [
                PermissionEnum::ClientsView->value,
                PermissionEnum::CredentialsView->value,
                PermissionEnum::CredentialsCreate->value,
                PermissionEnum::CredentialsReveal->value,
                PermissionEnum::CredentialsDelete->value,
            ],

            RoleEnum::Viewer->value => [
                PermissionEnum::ClientsView->value,
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions($permissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::query()
            ->whereIn('name', array_map(
                static fn (RoleEnum $role): string => $role->value,
                RoleEnum::cases(),
            ))
            ->delete();

        Permission::query()
            ->whereIn('name', PermissionEnum::values())
            ->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
