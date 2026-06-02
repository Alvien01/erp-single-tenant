<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\TenantInvitation;
use App\Models\TenantUser;
use App\Models\User;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaaSInvitationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::create([
            'name' => 'Premium Plan',
            'price_monthly' => 99000,
            'price_yearly' => 999000,
            'max_users' => 5,
            'max_products' => 100,
            'max_warehouses' => 5,
            'max_stores' => 5,
            'features' => ['all'],
            'is_active' => true,
        ]);

        $this->tenant = Tenant::create([
            'name' => 'Gede Corp',
            'slug' => 'gede-corp',
            'plan_id' => $this->plan->id,
            'is_active' => true,
        ]);
    }

    public function test_can_send_invitation(): void
    {
        app(TenantContext::class)->set($this->tenant);

        $invitation = TenantInvitation::create([
            'tenant_id' => $this->tenant->id,
            'email' => 'collaborator@example.com',
            'role' => 'manager',
            'token' => TenantInvitation::generateToken(),
            'expires_at' => now()->addDays(7),
        ]);

        $this->assertDatabaseHas('tenant_invitations', [
            'tenant_id' => $this->tenant->id,
            'email' => 'collaborator@example.com',
            'role' => 'manager',
        ]);

        $this->assertTrue($invitation->isValid());
    }

    public function test_logged_in_user_can_accept_invitation_instantly(): void
    {
        $user = User::create([
            'name' => 'Collaborator User',
            'email' => 'collaborator@example.com',
            'password' => bcrypt('password'),
        ]);

        $invitation = TenantInvitation::create([
            'tenant_id' => $this->tenant->id,
            'email' => 'collaborator@example.com',
            'role' => 'manager',
            'token' => TenantInvitation::generateToken(),
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->actingAs($user)
            ->get(route('invitations.accept', ['token' => $invitation->token]));

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tenant_users', [
            'tenant_id' => $this->tenant->id,
            'user_id' => $user->id,
            'role' => 'manager',
        ]);

        $this->assertNotNull($invitation->refresh()->accepted_at);
    }

    public function test_guest_is_redirected_to_register_with_session(): void
    {
        $invitation = TenantInvitation::create([
            'tenant_id' => $this->tenant->id,
            'email' => 'collaborator@example.com',
            'role' => 'manager',
            'token' => TenantInvitation::generateToken(),
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->get(route('invitations.accept', ['token' => $invitation->token]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHas('pending_invitation_token', $invitation->token);
    }
}
