<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_admin_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.products.index'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.categories.store'), ['name' => 'X'])->assertRedirect(route('admin.login'));
    }

    public function test_storefront_stays_public(): void
    {
        $this->get(route('shop.index'))->assertOk();
    }

    public function test_login_page_renders(): void
    {
        $this->get(route('admin.login'))->assertOk()->assertSee('Sign in');
    }

    public function test_user_can_login_and_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->create(['password' => 'secret123']);

        $this->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'secret123'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'wrong'])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 5) as $i) {
            $this->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'wrong']);
        }

        $this->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertStringContainsString('Too many', session('errors')->first('email'));
    }

    public function test_authenticated_user_is_redirected_away_from_login(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_user_can_logout(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }
}
