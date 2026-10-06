<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create([
            'email' => 'admin@uprs.test',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_valid_credentials_redirect_to_dashboard(): void
    {
        $this->makeUser();

        $response = $this->post(route('login'), [
            'email' => 'admin@uprs.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_invalid_credentials_show_error_and_stay_guest(): void
    {
        $this->makeUser();

        $response = $this->post(route('login'), [
            'email' => 'admin@uprs.test',
            'password' => 'salah-password',
        ]);

        $response->assertSessionHasErrors('email');
        $response->assertSessionMissing('success');
        $this->assertGuest();
    }

    public function test_login_is_throttled_after_five_attempts(): void
    {
        $this->makeUser();

        $payload = [
            'email' => 'admin@uprs.test',
            'password' => 'salah-password',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), $payload)->assertSessionHasErrors('email');
        }

        $this->post(route('login'), $payload)->assertStatus(429);
    }

    public function test_logout_ends_session_and_redirects_to_login(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
