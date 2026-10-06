# Software Requirements Specification (SRS)

**Aplikasi Peminjaman Laboratorium**
**Kelompok 3 — Pengembangan Perangkat Lunak**

## 1. Pendahuluan

### 1.1 Tujuan

Membangun sistem digital untuk mengelola peminjaman laboratorium dan alat agar lebih efektif, tertata, dan bebas dari double-booking.

### 1.2 Ruang Lingkup

Sistem mencakup:
- Pengajuan peminjaman oleh mahasiswa/dosen
- Verifikasi ketersediaan otomatis
- Persetujuan/penolakan oleh laboran
- Serah-terima dan pengembalian alat
- Riwayat dan laporan

### 1.3 Definisi

- Lab: Laboratorium, ruangan praktikum
- Alat: Perangkat yang dipinjam
- Booking: Pengajuan peminjaman

## 2. Deskripsi Umum

### 2.1 Aktor Sistem

| Aktor | Peran |
|-------|-------|
| Mahasiswa | Mengajukan peminjaman lab/alat |
| Dosen | Sama seperti mahasiswa |
| Laboran | Menyetujui, serah-terima, pengembalian |
| Kepala Lab | Memantau laporan |

### 2.2 Lingkungan Operasi

- Browser modern (Chrome, Firefox, Edge)
- Server: Docker + Nginx + PHP 8.4
- Database: MariaDB 10.11

## 3. Kebutuhan Fungsional

### UC-01: Mengajukan Peminjaman

- Aktor: Mahasiswa, Dosen
- Sistem mengecek ketersediaan (stok, durasi, jadwal bentrok)
- Data tersimpan dengan status menunggu

### UC-02: Cek Ketersediaan

- Cek stok alat lebih dari 0
- Cek durasi kurang dari atau sama dengan max_durasi_jam lab
- Cek tidak bentrok dengan booking aktif

### UC-03: Menyetujui atau Menolak

- Aktor: Laboran, Kalab
- Laboran bisa setujui, status berubah ke disetujui
- Laboran bisa tolak dengan alasan, status berubah ke ditolak

### UC-04: Mencatat Serah-Terima

- Aktor: Laboran
- Setelah disetujui, alat diserahkan
- Status berubah ke dipinjam, dicatat diambil_pada

### UC-05: Mencatat Pengembalian

- Aktor: Laboran
- Catat kondisi: baik, rusak_ringan, rusak_berat, hilang
- Catat keterlambatan otomatis
- Status berubah ke dikembalikan

### UC-06: Melihat Laporan

- Aktor: Kalab
- Statistik total, per status, per lab

## 4. Kebutuhan Non-Fungsional

| Aspek | Requirement |
|-------|-------------|
| Performa | Response kurang dari 2 detik untuk submit form |
| Keamanan | Password bcrypt, CSRF token, middleware auth |
| Usability | UI responsif desktop dan mobile, konsisten |
| Reliability | Anti double-booking, validasi stok |
| Maintainability | Kode modular, ada unit test 21 tests |

## 5. Use Case Diagram

Lihat slide halaman 11.

## 6. Arsitektur

- Pola: MVC (Model-View-Controller)
- Framework: Laravel 13
- Container: Docker Compose (4 service)
- Database: MariaDB dengan 5 tabel utama

## 7. Testing

- Unit Test: 21 test, 52 assertions, semua PASS
- UAT: 20 skenario, semua LULUS

## 8. Risiko dan Mitigasi

| Risiko | Mitigasi |
|--------|----------|
| Aturan lab berbeda | Workshop dengan laboran dan kalab |
| Double booking | Validasi jadwal otomatis |
| Scope melebar | Fokus 2 use case prioritas |
