# Dokumentasi Database

**Sistem Peminjaman Laboratorium**
**Kelompok 3 — PPL**

## Skema Utama

Sistem ini menggunakan **5 tabel utama** + tabel bawaan Laravel.

## 1. Tabel `users`

Menyimpan semua aktor sistem (4 role dalam 1 tabel).

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint unsigned | Primary key |
| nim_nip | varchar(30) | NIM mahasiswa / NIP dosen |
| name | varchar(255) | Nama lengkap |
| email | varchar(255) | Email unik |
| role | enum | mahasiswa, dosen, laboran, kalab |
| password | varchar(255) | Bcrypt hash |
| email_verified_at | timestamp | Nullable |
| remember_token | varchar(100) | Nullable |
| created_at | timestamp | Otomatis |
| updated_at | timestamp | Otomatis |

## 2. Tabel `labs`

Menyimpan data laboratorium.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint unsigned | Primary key |
| nama | varchar(100) | Nama lab |
| lokasi | varchar(100) | Gedung + lantai |
| jam_buka | time | Jam mulai operasional |
| jam_tutup | time | Jam tutup |
| max_durasi_jam | tinyint | Durasi maksimal peminjaman |
| boleh_weekend | boolean | Apakah lab bisa weekend |
| created_at | timestamp | Otomatis |
| updated_at | timestamp | Otomatis |

## 3. Tabel `equipment`

Menyimpan alat per lab.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint unsigned | Primary key |
| lab_id | bigint unsigned | FK ke labs |
| nama | varchar(100) | Nama alat |
| stok | int unsigned | Jumlah tersedia |
| keterangan | text | Nullable |
| created_at | timestamp | Otomatis |
| updated_at | timestamp | Otomatis |

## 4. Tabel `bookings`

Menyimpan pengajuan peminjaman.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint unsigned | Primary key |
| user_id | bigint unsigned | FK ke users |
| lab_id | bigint unsigned | FK ke labs |
| equipment_id | bigint unsigned | FK ke equipment |
| qty | int unsigned | Jumlah |
| mulai | datetime | Waktu mulai |
| selesai | datetime | Waktu selesai |
| keperluan | text | Deskripsi |
| status | enum | menunggu, disetujui, ditolak, dipinjam, dikembalikan |
| alasan_tolak | text | Nullable |
| diproses_oleh | bigint unsigned | FK ke users |
| diproses_pada | timestamp | Nullable |
| diambil_pada | timestamp | Nullable |
| dikembalikan_pada | timestamp | Nullable |
| kondisi_kembali | varchar(30) | baik, rusak_ringan, rusak_berat, hilang |
| catatan_kembali | text | Nullable |
| menit_telat | int unsigned | Default 0 |
| created_at | timestamp | Otomatis |
| updated_at | timestamp | Otomatis |

## 5. Tabel `booking_equipment` (PIVOT)

Relasi many-to-many antara bookings & equipment.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint unsigned | Primary key |
| booking_id | bigint unsigned | FK ke bookings |
| equipment_id | bigint unsigned | FK ke equipment |
| qty | int unsigned | Jumlah per alat |
| created_at | timestamp | Otomatis |
| updated_at | timestamp | Otomatis |

## Relasi Antar Tabel

- users (1) -> bookings (N)
- labs (1) -> equipment (N)
- labs (1) -> bookings (N)
- users laboran (1) -> bookings (N) via diproses_oleh
- bookings (N) <-> equipment (N) via booking_equipment

## Enum Values

- users.role: mahasiswa, dosen, laboran, kalab
- bookings.status: menunggu, disetujui, ditolak, dipinjam, dikembalikan
- bookings.kondisi_kembali: baik, rusak_ringan, rusak_berat, hilang
