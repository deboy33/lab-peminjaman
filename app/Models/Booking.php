<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'user_id', 'lab_id', 'equipment_id', 'qty', 'mulai', 'selesai',
        'keperluan', 'status', 'alasan_tolak', 'diproses_oleh',
        'diproses_pada', 'diambil_pada', 'dikembalikan_pada',
        'kondisi_kembali', 'catatan_kembali', 'menit_telat',
    ];

    protected $casts = [
        'mulai'             => 'datetime',
        'selesai'           => 'datetime',
        'diproses_pada'     => 'datetime',
        'diambil_pada'      => 'datetime',
        'dikembalikan_pada' => 'datetime',
    ];

    public function user()         { return $this->belongsTo(User::class); }
    public function lab()          { return $this->belongsTo(Lab::class); }
    public function equipment()    { return $this->belongsTo(Equipment::class); }
    public function diprosesOleh() { return $this->belongsTo(User::class, 'diproses_oleh'); }

    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'booking_equipment')
                    ->withPivot('qty')->withTimestamps();
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'menunggu'     => ['Menunggu',     'bg-amber-100 text-amber-800 ring-amber-200'],
            'disetujui'    => ['Disetujui',    'bg-emerald-100 text-emerald-800 ring-emerald-200'],
            'ditolak'      => ['Ditolak',      'bg-rose-100 text-rose-800 ring-rose-200'],
            'dipinjam'     => ['Dipinjam',     'bg-sky-100 text-sky-800 ring-sky-200'],
            'dikembalikan' => ['Dikembalikan', 'bg-slate-100 text-slate-700 ring-slate-200'],
            default        => [ucfirst((string) $this->status), 'bg-slate-100 text-slate-700 ring-slate-200'],
        };
    }
}
