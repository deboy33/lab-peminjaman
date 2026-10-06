<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Lab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HandoverReturnTest extends TestCase
{
    use RefreshDatabase;

    private function buatBooking(string $status = 'disetujui'): array
    {
        $mhs = User::factory()->create(['role' => 'mahasiswa']);
        $laboran = User::factory()->create(['role' => 'laboran']);
        $lab = Lab::create([
            'nama' => 'Lab HR', 'lokasi' => 'Gedung H',
            'jam_buka' => '08:00:00', 'jam_tutup' => '17:00:00',
            'max_durasi_jam' => 4, 'boleh_weekend' => false,
        ]);

        $booking = Booking::create([
            'user_id'   => $mhs->id,
            'lab_id'    => $lab->id,
            'mulai'     => now()->addDays(1),
            'selesai'   => now()->addDays(1)->addHours(2),
            'keperluan' => 'Test',
            'status'    => $status,
        ]);

        return [$booking, $laboran];
    }

    public function test_laboran_bisa_catat_serah_terima(): void
    {
        [$booking, $laboran] = $this->buatBooking('disetujui');

        $response = $this->actingAs($laboran)
                         ->post("/booking/{$booking->id}/handover");

        $response->assertRedirect();
        $this->assertEquals('dipinjam', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->diambil_pada);
    }

    public function test_laboran_bisa_catat_pengembalian(): void
    {
        [$booking, $laboran] = $this->buatBooking('dipinjam');

        $response = $this->actingAs($laboran)
                         ->post("/booking/{$booking->id}/return", [
                             'kondisi_kembali' => 'baik',
                             'catatan_kembali' => 'Alat lengkap',
                         ]);

        $response->assertRedirect();
        $booking->refresh();
        $this->assertEquals('dikembalikan', $booking->status);
        $this->assertEquals('baik', $booking->kondisi_kembali);
        $this->assertEquals('Alat lengkap', $booking->catatan_kembali);
        $this->assertNotNull($booking->dikembalikan_pada);
    }

    public function test_handover_gagal_kalau_status_bukan_disetujui(): void
    {
        [$booking, $laboran] = $this->buatBooking('menunggu');

        $response = $this->actingAs($laboran)
                         ->post("/booking/{$booking->id}/handover");

        $response->assertStatus(400);
    }

    public function test_return_gagal_kalau_status_bukan_dipinjam(): void
    {
        [$booking, $laboran] = $this->buatBooking('disetujui');

        $response = $this->actingAs($laboran)
                         ->post("/booking/{$booking->id}/return", [
                             'kondisi_kembali' => 'baik',
                         ]);

        $response->assertStatus(400);
    }
}
