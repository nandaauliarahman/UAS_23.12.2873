<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_requires_real_credentials(): void
    {
        Config::set('services.google.client_id', null);
        Config::set('services.google.client_secret', null);

        $response = $this->get(route('social.google.redirect'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'google-demo@example.com',
        ]);
    }

    public function test_google_callback_links_existing_user_and_logs_in(): void
    {
        Config::set('services.google.client_id', 'client-id');
        Config::set('services.google.client_secret', 'client-secret');

        $user = User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $googleUser = (new SocialiteUser())->setRaw([
            'sub' => 'google-123',
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'picture' => 'https://example.com/avatar.jpg',
        ])->map([
            'id' => 'google-123',
            'nickname' => null,
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);

        $response = $this->get(route('social.google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user->fresh());
        $this->assertDatabaseHas('users', [
            'email' => 'existing@example.com',
            'google_id' => 'google-123',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);
    }
}
