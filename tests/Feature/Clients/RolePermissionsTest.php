<?php

use App\Enums\Role;
use App\Models\Client;

describe('client permissions', function () {
    describe('viewing clients', function () {
        it('allows every authenticated role to list clients', function (Role $role) {
            $user = makeUserWithRole($role);

            $this->actingAs($user)
                ->get(route('clients.index'))
                ->assertOk();
        })->with(Role::cases());

        it('allows every authenticated role to view a client', function (Role $role) {
            $user = makeUserWithRole($role);
            $client = makeClient();

            $this->actingAs($user)
                ->get(route('clients.show', $client))
                ->assertOk();
        })->with(Role::cases());
    });

    describe('creating clients', function () {
        it('allows admin, manager and coordinator to open the create form', function (Role $role) {
            $user = makeUserWithRole($role);

            $this->actingAs($user)
                ->get(route('clients.create'))
                ->assertOk();
        })->with([
            'admin' => [Role::Admin],
            'manager' => [Role::Manager],
            'coordinator' => [Role::Coordinator],
        ]);

        it('denies developer, sales, marketer and viewer from opening the create form', function (Role $role) {
            $user = makeUserWithRole($role);

            $this->actingAs($user)
                ->get(route('clients.create'))
                ->assertForbidden();
        })->with([
            'developer' => [Role::Developer],
            'sales' => [Role::Sales],
            'marketer' => [Role::Marketer],
            'viewer' => [Role::Viewer],
        ]);

        it('allows admin and manager to assign a coordinator when storing a client', function (Role $role) {
            $user = makeUserWithRole($role);
            $coordinator = makeUserWithRole(Role::Coordinator);

            $this->actingAs($user)
                ->post(route('clients.store'), [
                    'name' => 'New Client',
                    'email' => 'new@example.com',
                    'phone' => null,
                    'notes' => null,
                    'coordinator_id' => $coordinator->id,
                ])
                ->assertRedirect();

            expect(Client::query()->where('name', 'New Client')->value('coordinator_id'))
                ->toBe($coordinator->id);
        })->with([
            'admin' => [Role::Admin],
            'manager' => [Role::Manager],
        ]);

        it('assigns the authenticated coordinator as coordinator when storing without coordinator_id', function () {
            $user = makeUserWithRole(Role::Coordinator);

            $this->actingAs($user)
                ->post(route('clients.store'), [
                    'name' => 'Coordinator Client',
                    'email' => 'coord@example.com',
                    'phone' => null,
                    'notes' => null,
                ])
                ->assertRedirect();

            expect(Client::query()->where('name', 'Coordinator Client')->value('coordinator_id'))
                ->toBe($user->id);
        });

        it('denies viewer from storing a client', function () {
            $user = makeUserWithRole(Role::Viewer);

            $this->actingAs($user)
                ->post(route('clients.store'), [
                    'name' => 'Blocked Client',
                    'email' => 'blocked@example.com',
                    'phone' => null,
                    'notes' => null,
                ])
                ->assertForbidden();

            expect(Client::query()->where('name', 'Blocked Client')->exists())->toBeFalse();
        });
    });

    describe('updating clients', function () {
        it('allows a coordinator to update their own client', function () {
            $coordinator = makeUserWithRole(Role::Coordinator);
            $client = makeClient($coordinator);

            $this->actingAs($coordinator)
                ->patch(route('clients.update', $client), validClientPayload([
                    'name' => 'Own Client Updated',
                ]))
                ->assertRedirect(route('clients.show', $client));

            expect($client->fresh()->name)->toBe('Own Client Updated');
        });

        it('denies a coordinator from updating another coordinators client', function () {
            $owner = makeUserWithRole(Role::Coordinator);
            $otherCoordinator = makeUserWithRole(Role::Coordinator);
            $client = makeClient($owner);

            $this->actingAs($otherCoordinator)
                ->patch(route('clients.update', $client), validClientPayload([
                    'name' => 'Should Not Update',
                ]))
                ->assertForbidden();

            expect($client->fresh()->name)->not->toBe('Should Not Update');
        });

        it('allows admin and manager to update any client', function (Role $role) {
            $actor = makeUserWithRole($role);
            $owner = makeUserWithRole(Role::Coordinator);
            $client = makeClient($owner);
            $newCoordinator = makeUserWithRole(Role::Coordinator);

            $this->actingAs($actor)
                ->patch(route('clients.update', $client), validClientPayload([
                    'name' => 'Updated By Privileged Role',
                    'coordinator_id' => $newCoordinator->id,
                ]))
                ->assertRedirect(route('clients.show', $client));

            $client->refresh();

            expect($client->name)->toBe('Updated By Privileged Role')
                ->and($client->coordinator_id)->toBe($newCoordinator->id);
        })->with([
            'admin' => [Role::Admin],
            'manager' => [Role::Manager],
        ]);

        it('denies viewer from updating a client', function () {
            $viewer = makeUserWithRole(Role::Viewer);
            $client = makeClient();

            $this->actingAs($viewer)
                ->patch(route('clients.update', $client), validClientPayload([
                    'name' => 'Viewer Update',
                ]))
                ->assertForbidden();
        });

        it('denies developer from updating a client', function () {
            $developer = makeUserWithRole(Role::Developer);
            $client = makeClient();

            $this->actingAs($developer)
                ->patch(route('clients.update', $client), validClientPayload([
                    'name' => 'Developer Update',
                ]))
                ->assertForbidden();
        });
    });

    describe('deleting clients', function () {
        it('allows a coordinator to delete their own client', function () {
            $coordinator = makeUserWithRole(Role::Coordinator);
            $client = makeClient($coordinator);

            $this->actingAs($coordinator)
                ->delete(route('clients.destroy', $client))
                ->assertRedirect(route('clients.index'));

            expect(Client::query()->find($client->id))->toBeNull();
        });

        it('denies a coordinator from deleting another coordinators client', function () {
            $owner = makeUserWithRole(Role::Coordinator);
            $otherCoordinator = makeUserWithRole(Role::Coordinator);
            $client = makeClient($owner);

            $this->actingAs($otherCoordinator)
                ->delete(route('clients.destroy', $client))
                ->assertForbidden();

            expect(Client::query()->find($client->id))->not->toBeNull();
        });

        it('allows admin to delete any client', function () {
            $admin = makeUserWithRole(Role::Admin);
            $client = makeClient();

            $this->actingAs($admin)
                ->delete(route('clients.destroy', $client))
                ->assertRedirect(route('clients.index'));

            expect(Client::query()->find($client->id))->toBeNull();
        });

        it('allows manager to delete only their own client', function () {
            $manager = makeUserWithRole(Role::Manager);
            $ownClient = makeClient($manager);
            $foreignClient = makeClient(makeUserWithRole(Role::Coordinator));

            $this->actingAs($manager)
                ->delete(route('clients.destroy', $ownClient))
                ->assertRedirect(route('clients.index'));

            $this->actingAs($manager)
                ->delete(route('clients.destroy', $foreignClient))
                ->assertForbidden();

            expect(Client::query()->find($ownClient->id))->toBeNull()
                ->and(Client::query()->find($foreignClient->id))->not->toBeNull();
        });

        it('denies viewer from deleting a client', function () {
            $viewer = makeUserWithRole(Role::Viewer);
            $client = makeClient();

            $this->actingAs($viewer)
                ->delete(route('clients.destroy', $client))
                ->assertForbidden();
        });
    });
});

describe('credential permissions', function () {
    it('allows roles with credential access to store credentials', function (Role $role) {
        $user = makeUserWithRole($role);
        $client = makeClient();

        $this->actingAs($user)
            ->post(route('clients.credentials.store', $client), [
                'name' => 'FTP',
                'description' => 'Server access',
                'login' => 'ftp-user',
                'password' => 'ftp-pass',
                'additional_information' => null,
                'url' => 'https://example.com',
            ])
            ->assertRedirect(route('clients.show', $client));

        expect($client->credentials()->where('name', 'FTP')->exists())->toBeTrue();
    })->with([
        'admin' => [Role::Admin],
        'manager' => [Role::Manager],
        'coordinator' => [Role::Coordinator],
        'developer' => [Role::Developer],
        'sales' => [Role::Sales],
        'marketer' => [Role::Marketer],
    ]);

    it('denies viewer from storing credentials', function () {
        $viewer = makeUserWithRole(Role::Viewer);
        $client = makeClient();

        $this->actingAs($viewer)
            ->post(route('clients.credentials.store', $client), [
                'name' => 'Blocked Credential',
                'description' => null,
                'login' => 'user',
                'password' => 'pass',
                'additional_information' => null,
            ])
            ->assertForbidden();

        expect($client->credentials()->where('name', 'Blocked Credential')->exists())->toBeFalse();
    });

    it('allows roles with credential access to reveal credentials', function (Role $role) {
        $user = makeUserWithRole($role);
        $client = makeClient();
        $credential = makeCredentialWithPayload($client, $user);

        $this->actingAs($user)
            ->getJson(route('clients.credentials.reveal', [$client, $credential]))
            ->assertOk()
            ->assertJsonPath('login', 'login')
            ->assertJsonPath('password', 'secret');
    })->with([
        'admin' => [Role::Admin],
        'manager' => [Role::Manager],
        'coordinator' => [Role::Coordinator],
        'developer' => [Role::Developer],
        'sales' => [Role::Sales],
        'marketer' => [Role::Marketer],
    ]);

    it('denies viewer from revealing credentials', function () {
        $viewer = makeUserWithRole(Role::Viewer);
        $owner = makeUserWithRole(Role::Admin);
        $client = makeClient();
        $credential = makeCredentialWithPayload($client, $owner);

        $this->actingAs($viewer)
            ->getJson(route('clients.credentials.reveal', [$client, $credential]))
            ->assertForbidden();
    });

    it('allows roles with credential access to delete credentials', function (Role $role) {
        $user = makeUserWithRole($role);
        $client = makeClient();
        $credential = makeCredentialWithPayload($client, $user);

        $this->actingAs($user)
            ->delete(route('clients.credentials.destroy', [$client, $credential]))
            ->assertRedirect(route('clients.show', $client));

        expect($client->credentials()->find($credential->id))->toBeNull();
    })->with([
        'admin' => [Role::Admin],
        'manager' => [Role::Manager],
        'coordinator' => [Role::Coordinator],
        'developer' => [Role::Developer],
        'sales' => [Role::Sales],
        'marketer' => [Role::Marketer],
    ]);

    it('denies viewer from deleting credentials', function () {
        $viewer = makeUserWithRole(Role::Viewer);
        $owner = makeUserWithRole(Role::Admin);
        $client = makeClient();
        $credential = makeCredentialWithPayload($client, $owner);

        $this->actingAs($viewer)
            ->delete(route('clients.credentials.destroy', [$client, $credential]))
            ->assertForbidden();

        expect($client->credentials()->find($credential->id))->not->toBeNull();
    });

    it('returns an empty credentials list for viewer on client show', function () {
        $viewer = makeUserWithRole(Role::Viewer);
        $owner = makeUserWithRole(Role::Admin);
        $client = makeClient();
        makeCredentialWithPayload($client, $owner);

        $this->actingAs($viewer)
            ->get(route('clients.show', $client))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('clients/show')
                ->where('credentials', []));
    });

    it('returns credentials for admin on client show', function () {
        $admin = makeUserWithRole(Role::Admin);
        $client = makeClient();
        makeCredentialWithPayload($client, $admin);

        $this->actingAs($admin)
            ->get(route('clients.show', $client))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('clients/show')
                ->has('credentials', 1));
    });
});

describe('coordinator assignment permissions', function () {
    it('shares coordinators list only for admin and manager on create', function (Role $role) {
        $user = makeUserWithRole($role);
        makeUserWithRole(Role::Coordinator);

        $this->actingAs($user)
            ->get(route('clients.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('coordinators'));
    })->with([
        'admin' => [Role::Admin],
        'manager' => [Role::Manager],
    ]);

    it('does not share coordinators list for coordinator on create', function () {
        $user = makeUserWithRole(Role::Coordinator);

        $this->actingAs($user)
            ->get(route('clients.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->missing('coordinators'));
    });

    it('denies viewer access to create form', function () {
        $user = makeUserWithRole(Role::Viewer);

        $this->actingAs($user)
            ->get(route('clients.create'))
            ->assertForbidden();
    });

    it('does not allow coordinator to change coordinator on update', function () {
        $coordinator = makeUserWithRole(Role::Coordinator);
        $otherCoordinator = makeUserWithRole(Role::Coordinator);
        $client = makeClient($coordinator);

        $this->actingAs($coordinator)
            ->patch(route('clients.update', $client), validClientPayload([
                'name' => 'Still Same Coordinator',
                'coordinator_id' => $otherCoordinator->id,
            ]))
            ->assertRedirect(route('clients.show', $client));

        expect($client->fresh()->coordinator_id)->toBe($coordinator->id);
    });
});
