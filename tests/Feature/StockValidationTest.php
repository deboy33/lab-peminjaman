<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\Lab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_gagal_kalau_qty_lebih_dari_stok(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $lab = Lab::create([
            'nama' => 'Lab A', 'lokasi' => 'Gedung A',
            'jam_buka' => '08:00:00', 'jam_tutup' => '17:00:00',
            'max_durasi_jam' => 4, 'boleh_weekend' => false,
        ]);
        $eq = Equipment::create([
            'lab_id' => $lab->id,
            'nama'   => 'Alat Terbatas',
            'stok'   => 3,
        ]);

        $response = $this->actingAs($user)->post('/booking', [
            'lab_id'    => $lab->id,
            'mulai'     => now()->addDays(1)->format('Y-m-d H:i:s'),
            'selesai'   => now()->addDays(1)->addHours(2)->format('Y-m-d H:i:s'),
            'keperluan' => 'Test',
            'equipments' => [
                ['id' => $eq->id, 'qty' => 10],
            ],
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_booking_sukses_kalau_qty_dalam_batas(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $lab = Lab::create([
            'nama' => 'Lab B', 'lokasi' => 'Gedung B',
            'jam_buka' => '08:00:00', 'jam_tutup' => '17:00:00',
            'max_durasi_jam' => 4, 'boleh_weekend' => false,
        ]);
        $eq = Equipment::create([
            'lab_id' => $lab->id,
            'nama'   => 'Alat Cukup',
            'stok'   => 5,
        ]);

        $response = $this->actingAs($user)->post('/booking', [
            'lab_id'    => $lab->id,
            'mulai'     => now()->addDays(1)->format('Y-m-d H:i:s'),
            'selesai'   => now()->addDays(1)->addHours(2)->format('Y-m-d H:i:s'),
            'keperluan' => 'Test',
            'equipments' => [
                ['id' => $eq->id, 'qty' => 3],
            ],
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('bookings', 1);
    }
}
