<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Lab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoubleBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_bentrok_ditolak(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $lab = Lab::create([
            'nama' => 'Lab Bentrok', 'lokasi' => 'Gedung X',
            'jam_buka' => '08:00:00', 'jam_tutup' => '17:00:00',
            'max_durasi_jam' => 8, 'boleh_weekend' => true,
        ]);
        $eq = Equipment::create([
            'lab_id' => $lab->id,
            'nama'   => 'Alat X',
            'stok'   => 5,
        ]);

        // Booking existing: besok jam 13:00 - 15:00
        Booking::create([
            'user_id'   => $user->id,
            'lab_id'    => $lab->id,
            'mulai'     => now()->addDays(1)->setTime(13, 0),
            'selesai'   => now()->addDays(1)->setTime(15, 0),
            'keperluan' => 'Existing',
            'status'    => 'menunggu',
        ]);

        // Coba booking baru: besok jam 14:00 - 16:00 (bentrok)
        $response = $this->actingAs($user)->post('/booking', [
            'lab_id'    => $lab->id,
            'mulai'     => now()->addDays(1)->setTime(14, 0)->format('Y-m-d H:i:s'),
            'selesai'   => now()->addDays(1)->setTime(16, 0)->format('Y-m-d H:i:s'),
            'keperluan' => 'Coba bentrok',
            'equipments' => [['id' => $eq->id, 'qty' => 1]],
        ]);

        $response->assertSessionHasErrors('selesai');
        $this->assertDatabaseCount('bookings', 1); // hanya existing
    }

    public function test_booking_tidak_bentrok_diterima(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $lab = Lab::create([
            'nama' => 'Lab OK', 'lokasi' => 'Gedung Y',
            'jam_buka' => '08:00:00', 'jam_tutup' => '17:00:00',
            'max_durasi_jam' => 8, 'boleh_weekend' => true,
        ]);
        $eq = Equipment::create([
            'lab_id' => $lab->id,
            'nama'   => 'Alat Y',
            'stok'   => 5,
        ]);

        Booking::create([
            'user_id'   => $user->id,
            'lab_id'    => $lab->id,
            'mulai'     => now()->addDays(1)->setTime(13, 0),
            'selesai'   => now()->addDays(1)->setTime(15, 0),
            'keperluan' => 'Existing',
            'status'    => 'menunggu',
        ]);

        // Booking baru: 2 hari lagi (beda hari)
        $response = $this->actingAs($user)->post('/booking', [
            'lab_id'    => $lab->id,
            'mulai'     => now()->addDays(2)->setTime(13, 0)->format('Y-m-d H:i:s'),
            'selesai'   => now()->addDays(2)->setTime(15, 0)->format('Y-m-d H:i:s'),
            'keperluan' => 'Tidak bentrok',
            'equipments' => [['id' => $eq->id, 'qty' => 1]],
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('bookings', 2);
    }
}
