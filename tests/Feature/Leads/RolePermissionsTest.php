<?php

use App\Enums\LeadLabel;
use App\Enums\Role;
use App\Models\Lead;
use App\Models\User;

describe('lead permissions', function () {
    describe('viewing leads', function () {
        it('allows admin, manager and sales to list leads', function (Role $role) {
            $user = makeUserWithRole($role);
            $url = $role === Role::Sales
                ? route('leads.index', ['mine' => 1])
                : route('leads.index');

            $this->actingAs($user)
                ->get($url)
                ->assertOk()
                ->assertInertia(fn ($page) => $page->component('leads/index'));
        })->with([
            'admin' => [Role::Admin],
            'manager' => [Role::Manager],
            'sales' => [Role::Sales],
        ]);

        it('denies other roles from listing leads', function (Role $role) {
            $user = makeUserWithRole($role);

            $this->actingAs($user)
                ->get(route('leads.index'))
                ->assertForbidden();
        })->with([
            'coordinator' => [Role::Coordinator],
            'developer' => [Role::Developer],
            'marketer' => [Role::Marketer],
            'viewer' => [Role::Viewer],
        ]);

        it('redirects sales to mine filter by default', function () {
            $sales = makeUserWithRole(Role::Sales);

            $this->actingAs($sales)
                ->get(route('leads.index'))
                ->assertRedirect(route('leads.index', ['mine' => 1]));
        });

        it('shows only own leads to sales', function () {
            $sales = makeUserWithRole(Role::Sales);
            $otherSales = makeUserWithRole(Role::Sales);
            $ownLead = makeLead($sales);
            makeLead($otherSales);

            $this->actingAs($sales)
                ->get(route('leads.index', ['mine' => 1]))
                ->assertOk()
                ->assertInertia(fn ($page) => $page
                    ->has('leads', 1)
                    ->where('leads.0.id', $ownLead->id)
                    ->where('filters.mine', true));
        });

        it('shows all leads to manager by default', function () {
            $manager = makeUserWithRole(Role::Manager);
            makeLead();
            makeLead();

            $this->actingAs($manager)
                ->get(route('leads.index'))
                ->assertOk()
                ->assertInertia(fn ($page) => $page
                    ->has('leads', 2)
                    ->where('filters.mine', false));
        });

        it('allows manager to filter only own leads', function () {
            $manager = makeUserWithRole(Role::Manager);
            $sales = makeUserWithRole(Role::Sales);
            makeLead($manager);
            makeLead($sales);

            $this->actingAs($manager)
                ->get(route('leads.index', ['mine' => 1]))
                ->assertOk()
                ->assertInertia(fn ($page) => $page
                    ->has('leads', 1)
                    ->where('filters.mine', true));
        });
    });

    describe('creating leads', function () {
        it('allows sales to create a lead assigned to themselves', function () {
            $sales = makeUserWithRole(Role::Sales);

            $this->actingAs($sales)
                ->post(route('leads.store'), validLeadPayload([
                    'name' => 'Sales Lead',
                    'email' => 'sales-lead@example.com',
                    'label' => LeadLabel::New->value,
                ]))
                ->assertRedirect(route('leads.index', ['mine' => 1]));

            $lead = Lead::query()->where('email', 'sales-lead@example.com')->first();

            expect($lead)->not->toBeNull()
                ->and($lead?->sales_id)->toBe($sales->id)
                ->and($lead?->label)->toBe(LeadLabel::New);
        });

        it('allows manager to assign lead to a sales person', function () {
            $manager = makeUserWithRole(Role::Manager);
            $sales = makeUserWithRole(Role::Sales);

            $this->actingAs($manager)
                ->post(route('leads.store'), validLeadPayload([
                    'name' => 'Assigned Lead',
                    'email' => 'assigned-lead@example.com',
                    'label' => LeadLabel::Interested->value,
                    'sales_id' => $sales->id,
                ]))
                ->assertRedirect(route('leads.index'));

            expect(Lead::query()->where('email', 'assigned-lead@example.com')->value('sales_id'))
                ->toBe($sales->id);
        });

        it('allows manager to create a lead without assigning a sales person', function () {
            $manager = makeUserWithRole(Role::Manager);

            $this->actingAs($manager)
                ->post(route('leads.store'), validLeadPayload([
                    'name' => 'Unassigned Lead',
                    'email' => 'unassigned-lead@example.com',
                    'label' => LeadLabel::New->value,
                ]))
                ->assertRedirect(route('leads.index'));

            expect(Lead::query()->where('email', 'unassigned-lead@example.com')->value('sales_id'))
                ->toBeNull();
        });

        it('allows manager to clear sales person on update', function () {
            $manager = makeUserWithRole(Role::Manager);
            $sales = makeUserWithRole(Role::Sales);
            $lead = makeLead($sales);

            $this->actingAs($manager)
                ->patch(route('leads.update', $lead), validLeadPayload([
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'label' => $lead->label->value,
                    'sales_id' => '',
                ]))
                ->assertRedirect(route('leads.index'));

            expect($lead->fresh()->sales_id)->toBeNull();
        });

        it('denies coordinator from creating leads', function () {
            $coordinator = makeUserWithRole(Role::Coordinator);

            $this->actingAs($coordinator)
                ->post(route('leads.store'), validLeadPayload())
                ->assertForbidden();
        });
    });

    describe('updating leads', function () {
        it('allows sales to edit own lead', function () {
            $sales = makeUserWithRole(Role::Sales);
            $lead = makeLead($sales);

            $this->actingAs($sales)
                ->get(route('leads.edit', $lead))
                ->assertOk()
                ->assertInertia(fn ($page) => $page->component('leads/edit'));

            $this->actingAs($sales)
                ->patch(route('leads.update', $lead), validLeadPayload([
                    'name' => 'Updated Lead',
                    'email' => $lead->email,
                    'label' => LeadLabel::Negotiations->value,
                ]))
                ->assertRedirect(route('leads.index', ['mine' => 1]));

            expect($lead->fresh()->name)->toBe('Updated Lead')
                ->and($lead->fresh()->label)->toBe(LeadLabel::Negotiations);
        });

        it('denies sales from editing another sales lead', function () {
            $sales = makeUserWithRole(Role::Sales);
            $otherSales = makeUserWithRole(Role::Sales);
            $lead = makeLead($otherSales);

            $this->actingAs($sales)
                ->get(route('leads.edit', $lead))
                ->assertForbidden();

            $this->actingAs($sales)
                ->patch(route('leads.update', $lead), validLeadPayload([
                    'name' => 'Blocked Update',
                    'email' => $lead->email,
                ]))
                ->assertForbidden();
        });

        it('allows manager to edit any lead', function () {
            $manager = makeUserWithRole(Role::Manager);
            $sales = makeUserWithRole(Role::Sales);
            $lead = makeLead($sales);

            $this->actingAs($manager)
                ->patch(route('leads.update', $lead), validLeadPayload([
                    'name' => 'Manager Updated',
                    'email' => $lead->email,
                    'label' => LeadLabel::OfferSent->value,
                ]))
                ->assertRedirect(route('leads.index'));

            expect($lead->fresh()->name)->toBe('Manager Updated')
                ->and($lead->fresh()->label)->toBe(LeadLabel::OfferSent);
        });
    });

    describe('updating lead label', function () {
        it('allows sales to quickly update label on own lead', function () {
            $sales = makeUserWithRole(Role::Sales);
            $lead = makeLead($sales)->fresh();

            $this->actingAs($sales)
                ->from(route('leads.index', ['mine' => 1]))
                ->patch(route('leads.label.update', $lead), [
                    'label' => LeadLabel::Acquired->value,
                ])
                ->assertRedirect(route('leads.index', ['mine' => 1]));

            expect($lead->fresh()->label)->toBe(LeadLabel::Acquired);
        });

        it('denies sales from updating label on another sales lead', function () {
            $sales = makeUserWithRole(Role::Sales);
            $lead = makeLead(makeUserWithRole(Role::Sales));

            $this->actingAs($sales)
                ->patch(route('leads.label.update', $lead), [
                    'label' => LeadLabel::Rejected->value,
                ])
                ->assertForbidden();
        });
    });
});
