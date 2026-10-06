<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lab extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama', 'lokasi', 'jam_buka', 'jam_tutup',
        'max_durasi_jam', 'boleh_weekend',
    ];

    protected function casts(): array
    {
        return ['boleh_weekend' => 'boolean'];
    }

    public function equipments(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
