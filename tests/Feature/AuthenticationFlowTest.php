<?php

namespace Tests\Feature;

use App\Mail\ClientVerificationMail;
use App\Mail\StoreOwnerApproved;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test client registration with email verification
     *
     * @return void
     */
    public function test_client_registration_sends_verification_email()
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Test Client',
            'email' => 'testclient@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'tipo_usuario' => 'cliente',
        ]);

        $response->assertRedirect();

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'email' => 'testclient@example.com',
            'tipo_usuario' => 'cliente',
            'categoria_cliente' => 'Inicial', // Default category
        ]);

        // Assert verification email was sent
        Mail::assertSent(ClientVerificationMail::class, function ($mail) {
            return $mail->client->email === 'testclient@example.com';
        });

        $user = User::where('email', 'testclient@example.com')->first();
        $this->assertNull($user->email_verified_at);
    }

    /**
     * Test store owner registration requires approval
     *
     * @return void
     */
    public function test_store_owner_registration_requires_admin_approval()
    {
        $response = $this->post('/register', [
            'name' => 'Test Store Owner',
            'email' => 'testowner@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'tipo_usuario' => 'dueño de local',
        ]);

        $response->assertRedirect();

        // Assert user was created but not approved
        $this->assertDatabaseHas('users', [
            'email' => 'testowner@example.com',
            'tipo_usuario' => 'dueño de local',
        ]);

        $user = User::where('email', 'testowner@example.com')->first();
        $this->assertNull($user->approved_at); // Not approved yet
    }

    /**
     * Test admin can approve store owner
     *
     * @return void
     */
    public function test_admin_can_approve_store_owner()
    {
        Mail::fake();

        // Create pending store owner
        $storeOwner = User::factory()->pendingStoreOwner()->create();

        // Create admin
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->post(route('admin.store-owners.approve', $storeOwner->id));

        $response->assertRedirect();

        $storeOwner->refresh();
        $this->assertNotNull($storeOwner->approved_at);

        // Assert approval email was sent
        Mail::assertSent(StoreOwnerApproved::class, function ($mail) use ($storeOwner) {
            return $mail->user->id === $storeOwner->id;
        });
    }

    /**
     * Test unapproved store owner cannot access store dashboard
     *
     * @return void
     */
    public function test_unapproved_store_owner_cannot_access_dashboard()
    {
        $unapprovedOwner = User::factory()->pendingStoreOwner()->create();

        $this->actingAs($unapprovedOwner);

        $response = $this->get('/store/dashboard');

        $response->assertStatus(403); // Forbidden
    }

    /**
     * Test approved store owner can access dashboard
     *
     * @return void
     */
    public function test_approved_store_owner_can_access_dashboard()
    {
        $approvedOwner = User::factory()->storeOwner()->create();

        $this->actingAs($approvedOwner);

        $response = $this->get('/store/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test client must verify email to request promotions
     *
     * @return void
     */
    public function test_unverified_client_cannot_request_promotions()
    {
        $unverifiedClient = User::factory()->unverified()->create();

        $this->actingAs($unverifiedClient);

        $response = $this->get('/client/promotions');

        $response->assertRedirect('/verify-email'); // Redirect to verification page
    }

    /**
     * Test role-based access control
     *
     * @return void
     */
    public function test_clients_cannot_access_admin_routes()
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(403); // Forbidden
    }

    /**
     * Test store owners cannot access admin routes
     *
     * @return void
     */
    public function test_store_owners_cannot_access_admin_routes()
    {
        $owner = User::factory()->storeOwner()->create();

        $this->actingAs($owner);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(403); // Forbidden
    }

    /**
     * Test password requirements
     *
     * @return void
     */
    public function test_password_must_meet_requirements()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short', // Too short (< 8 characters)
            'password_confirmation' => 'short',
            'tipo_usuario' => 'cliente',
        ]);

        $response->assertSessionHasErrors(['password']);

        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test email uniqueness
     *
     * @return void
     */
    public function test_email_must_be_unique()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com', // Already exists
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'tipo_usuario' => 'cliente',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test login with correct credentials
     *
     * @return void
     */
    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login fails with incorrect credentials
     *
     * @return void
     */
    public function test_login_fails_with_incorrect_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}
