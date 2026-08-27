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

        $permission = Permission::findOrCreate(PermissionEnum::ClientsUpdateAny->value);
        $role = Role::findOrCreate(RoleEnum::Manager->value);
        $role->givePermissionTo($permission);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::query()->where('name', RoleEnum::Manager->value)->first();

        if ($role !== null) {
            $role->revokePermissionTo(PermissionEnum::ClientsUpdateAny->value);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
