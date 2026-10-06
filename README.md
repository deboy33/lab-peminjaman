# Aplikasi Peminjaman Laboratorium

Sistem digital pengelolaan peminjaman laboratorium & alat.

**Tugas Kelompok 3 — Pengembangan Perangkat Lunak (PPL)**

## Anggota

| Nama | NIM | Peran |
|------|-----|-------|
| Deby Hendri Yarto | 20220801294 | Pemrograman |
| Semiggi Sayang Koktar Putri Darmoko | 20240801286 | UI Frontend |
| Muhammad Fidel Herizal | 20240801270 | Dokumentasi/Analisis |
| Achmad Shyallvino Akbar | 20240801081 | Desain Database |

## Fitur

- Login multi-role: Mahasiswa, Dosen, Laboran, Kepala Lab
- Pengajuan peminjaman multi-alat dengan cek ketersediaan otomatis
- Anti double-booking
- Persetujuan / penolakan oleh laboran
- Serah-terima & pengembalian alat
- Dashboard statistik & riwayat

## Tech Stack

- Laravel 13 (PHP 8.4)
- MariaDB 10.11
- Nginx
- Tailwind CSS + Alpine.js (CDN)
- Docker Compose

## Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Mahasiswa | mahasiswa@lab.test | password |
| Dosen | dosen@lab.test | password |
| Laboran | laboran@lab.test | password |
| Kepala Lab | kalab@lab.test | password |

## Cara Menjalankan

- git clone <repo-url>
- cd lab-peminjaman
- cp .env.example .env
- docker compose up -d
- docker compose exec app php artisan migrate --seed

Akses: http://localhost:8080

## Testing

- docker compose exec app php artisan test

21 tests, 52 assertions, semua PASS.

## Metodologi

Rational Unified Process (RUP) — 4 fase:
1. Inception — analisis masalah, aktor, use case
2. Elaboration — SRS, arsitektur, UML, prototype UI
3. Construction — coding, unit test, integration test
4. Transition — deployment, UAT, pelatihan
