<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Lab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private function buatUser(): User
    {
        return User::factory()->create(['role' => 'mahasiswa']);
    }

    private function buatLab(): Lab
    {
        return Lab::create([
            'nama'           => 'Lab Test',
            'lokasi'         => 'Gedung Z',
            'jam_buka'       => '08:00:00',
            'jam_tutup'      => '17:00:00',
            'max_durasi_jam' => 4,
            'boleh_weekend'  => false,
        ]);
    }

    private function buatAlat(Lab $lab): Equipment
    {
        return Equipment::create([
            'lab_id' => $lab->id,
            'nama'   => 'Alat Test',
            'stok'   => 5,
        ]);
    }

    public function test_mahasiswa_bisa_submit_pengajuan(): void
    {
        $user = $this->buatUser();
        $lab = $this->buatLab();
        $eq = $this->buatAlat($lab);

        $response = $this->actingAs($user)->post('/booking', [
            'lab_id'             => $lab->id,
            'mulai'              => now()->addDays(1)->format('Y-m-d H:i:s'),
            'selesai'            => now()->addDays(1)->addHours(2)->format('Y-m-d H:i:s'),
            'keperluan'          => 'Test praktikum',
            'equipments'         => [
                ['id' => $eq->id, 'qty' => 1],
            ],
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'lab_id'  => $lab->id,
            'status'  => 'menunggu',
        ]);
        $this->assertDatabaseHas('booking_equipment', [
            'equipment_id' => $eq->id,
            'qty'          => 1,
        ]);
    }

    public function test_booking_gagal_tanpa_alat(): void
    {
        $user = $this->buatUser();
        $lab = $this->buatLab();

        $response = $this->actingAs($user)->post('/booking', [
            'lab_id'    => $lab->id,
            'mulai'     => now()->addDays(1)->format('Y-m-d H:i:s'),
            'selesai'   => now()->addDays(1)->addHours(2)->format('Y-m-d H:i:s'),
            'keperluan' => 'Test',
            'equipments' => [],
        ]);

        $response->assertSessionHasErrors('equipments');
    }

    public function test_booking_gagal_kalau_durasi_lebih_dari_maks(): void
    {
        $user = $this->buatUser();
        $lab = $this->buatLab(); // max 4 jam
        $eq = $this->buatAlat($lab);

        $response = $this->actingAs($user)->post('/booking', [
            'lab_id'    => $lab->id,
            'mulai'     => now()->addDays(1)->format('Y-m-d H:i:s'),
            'selesai'   => now()->addDays(1)->addHours(10)->format('Y-m-d H:i:s'),
            'keperluan' => 'Test',
            'equipments' => [['id' => $eq->id, 'qty' => 1]],
        ]);

        $response->assertSessionHasErrors('selesai');
    }

    public function test_laboran_bisa_approve_pengajuan(): void
    {
        $mhs = User::factory()->create(['role' => 'mahasiswa']);
        $laboran = User::factory()->create(['role' => 'laboran']);
        $lab = $this->buatLab();

        $booking = Booking::create([
            'user_id'   => $mhs->id,
            'lab_id'    => $lab->id,
            'mulai'     => now()->addDays(1),
            'selesai'   => now()->addDays(1)->addHours(2),
            'keperluan' => 'Test',
            'status'    => 'menunggu',
        ]);

        $response = $this->actingAs($laboran)
                         ->post("/booking/{$booking->id}/approve");

        $response->assertRedirect();
        $this->assertEquals('disetujui', $booking->fresh()->status);
        $this->assertEquals($laboran->id, $booking->fresh()->diproses_oleh);
    }

    public function test_laboran_bisa_reject_pengajuan(): void
    {
        $mhs = User::factory()->create(['role' => 'mahasiswa']);
        $laboran = User::factory()->create(['role' => 'laboran']);
        $lab = $this->buatLab();

        $booking = Booking::create([
            'user_id'   => $mhs->id,
            'lab_id'    => $lab->id,
            'mulai'     => now()->addDays(1),
            'selesai'   => now()->addDays(1)->addHours(2),
            'keperluan' => 'Test',
            'status'    => 'menunggu',
        ]);

        $response = $this->actingAs($laboran)
                         ->post("/booking/{$booking->id}/reject", [
                             'alasan_tolak' => 'Alat sedang maintenance',
                         ]);

        $response->assertRedirect();
        $this->assertEquals('ditolak', $booking->fresh()->status);
        $this->assertEquals('Alat sedang maintenance', $booking->fresh()->alasan_tolak);
    }

    public function test_mahasiswa_tidak_bisa_approve(): void
    {
        $mhs = User::factory()->create(['role' => 'mahasiswa']);
        $lab = $this->buatLab();

        $booking = Booking::create([
            'user_id'   => $mhs->id,
            'lab_id'    => $lab->id,
            'mulai'     => now()->addDays(1),
            'selesai'   => now()->addDays(1)->addHours(2),
            'keperluan' => 'Test',
            'status'    => 'menunggu',
        ]);

        $response = $this->actingAs($mhs)
                         ->post("/booking/{$booking->id}/approve");

        $response->assertStatus(403);
    }
}
