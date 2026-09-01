<?php

use App\Enums\Role;
use App\Models\User;
use Laravel\Fortify\Features;

describe('user management permissions', function () {
    describe('viewing users', function () {
        it('allows admin to list users', function () {
            $admin = makeUserWithRole(Role::Admin);

            $this->actingAs($admin)
                ->get(route('users.index'))
                ->assertOk()
                ->assertInertia(fn ($page) => $page->component('users/index'));
        });

        it('denies non-admin roles from listing users', function (Role $role) {
            $user = makeUserWithRole($role);

            $this->actingAs($user)
                ->get(route('users.index'))
                ->assertForbidden();
        })->with([
            'manager' => [Role::Manager],
            'coordinator' => [Role::Coordinator],
            'developer' => [Role::Developer],
            'sales' => [Role::Sales],
            'marketer' => [Role::Marketer],
            'viewer' => [Role::Viewer],
        ]);
    });

    describe('creating users', function () {
        it('allows admin to create a user with a role', function () {
            $admin = makeUserWithRole(Role::Admin);

            $this->actingAs($admin)
                ->post(route('users.store'), [
                    'name' => 'New User',
                    'email' => 'new-user@example.com',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                    'role' => Role::Viewer->value,
                ])
                ->assertRedirect(route('users.index'));

            $created = User::query()->where('email', 'new-user@example.com')->first();

            expect($created)->not->toBeNull()
                ->and($created?->hasRole(Role::Viewer->value))->toBeTrue();
        });

        it('denies manager from creating users', function () {
            $manager = makeUserWithRole(Role::Manager);

            $this->actingAs($manager)
                ->post(route('users.store'), [
                    'name' => 'Blocked User',
                    'email' => 'blocked-user@example.com',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                    'role' => Role::Viewer->value,
                ])
                ->assertForbidden();
        });
    });

    describe('updating users', function () {
        it('allows admin to update a user and change role', function () {
            $admin = makeUserWithRole(Role::Admin);
            $target = User::factory()->create();
            $target->assignRole(Role::Viewer->value);

            $this->actingAs($admin)
                ->patch(route('users.update', $target), [
                    'name' => 'Updated Name',
                    'email' => 'updated@example.com',
                    'role' => Role::Coordinator->value,
                ])
                ->assertRedirect(route('users.index'));

            $target->refresh();

            expect($target->name)->toBe('Updated Name')
                ->and($target->email)->toBe('updated@example.com')
                ->and($target->hasRole(Role::Coordinator->value))->toBeTrue();
        });

        it('denies coordinator from updating users', function () {
            $coordinator = makeUserWithRole(Role::Coordinator);
            $target = User::factory()->create();

            $this->actingAs($coordinator)
                ->patch(route('users.update', $target), [
                    'name' => 'Should Fail',
                    'email' => $target->email,
                    'role' => Role::Viewer->value,
                ])
                ->assertForbidden();
        });
    });

    describe('banning users', function () {
        it('allows admin to ban and unban another user', function () {
            $admin = makeUserWithRole(Role::Admin);
            $target = User::factory()->create();

            $this->actingAs($admin)
                ->patch(route('users.ban', $target))
                ->assertRedirect(route('users.index'));

            expect($target->fresh()->banned_at)->not->toBeNull();

            $this->actingAs($admin)
                ->delete(route('users.unban', $target))
                ->assertRedirect(route('users.index'));

            expect($target->fresh()->banned_at)->toBeNull();
        });

        it('denies admin from banning themselves', function () {
            $admin = makeUserWithRole(Role::Admin);

            $this->actingAs($admin)
                ->patch(route('users.ban', $admin))
                ->assertForbidden();
        });

        it('prevents banned users from logging in', function () {
            $user = User::factory()->create([
                'email' => 'banned@example.com',
                'banned_at' => now(),
            ]);

            $this->post(route('login.store'), [
                'email' => 'banned@example.com',
                'password' => 'password',
            ])->assertSessionHasErrors('email');

            $this->assertGuest();
        });

        it('logs out banned users on subsequent requests', function () {
            $admin = makeUserWithRole(Role::Admin);
            $target = User::factory()->create();

            $this->actingAs($target)
                ->get(route('dashboard'))
                ->assertOk();

            $target->update(['banned_at' => now()]);

            $this->actingAs($target)
                ->get(route('dashboard'))
                ->assertRedirect(route('login'));
        });
    });

    describe('deleting users', function () {
        it('allows admin to delete another user', function () {
            $admin = makeUserWithRole(Role::Admin);
            $target = User::factory()->create();

            $this->actingAs($admin)
                ->delete(route('users.destroy', $target))
                ->assertRedirect(route('users.index'));

            expect(User::query()->find($target->id))->toBeNull();
        });

        it('denies admin from deleting themselves', function () {
            $admin = makeUserWithRole(Role::Admin);

            $this->actingAs($admin)
                ->delete(route('users.destroy', $admin))
                ->assertForbidden();
        });

        it('denies manager from deleting users', function () {
            $manager = makeUserWithRole(Role::Manager);
            $target = User::factory()->create();

            $this->actingAs($manager)
                ->delete(route('users.destroy', $target))
                ->assertForbidden();
        });
    });
});

describe('registration', function () {
    it('is disabled in fortify config', function () {
        expect(Features::enabled(Features::registration()))->toBeFalse();
    });
});
