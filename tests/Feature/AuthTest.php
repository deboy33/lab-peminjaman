<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_bisa_login_dengan_kredensial_benar(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@lab.test',
            'password' => bcrypt('password'),
            'role'     => 'mahasiswa',
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@lab.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        User::factory()->create([
            'email'    => 'test@lab.test',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@lab.test',
            'password' => 'salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_belum_login_diarahkan_ke_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_laboran_bisa_akses_route_approve(): void
    {
        $laboran = User::factory()->create(['role' => 'laboran']);

        $response = $this->actingAs($laboran)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_logout_berhasil(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
