# User Acceptance Test (UAT)

Tabel skenario pengujian sistem dari sisi pengguna.

**Status:** Semua skenario LULUS.

## Skenario Pengujian

| No | Aktor | Skenario | Hasil yang Diharapkan | Status |
|----|-------|----------|----------------------|--------|
| 1 | Mahasiswa | Login kredensial benar | Masuk ke dashboard | LULUS |
| 2 | Mahasiswa | Login password salah | Muncul error | LULUS |
| 3 | Mahasiswa | Akses dashboard tanpa login | Redirect ke login | LULUS |
| 4 | Mahasiswa | Ajukan peminjaman lengkap | Data tersimpan, status Menunggu | LULUS |
| 5 | Mahasiswa | Ajukan tanpa pilih alat | Error minimal 1 alat | LULUS |
| 6 | Mahasiswa | Pilih lab untuk lihat alat | Dropdown alat terisi | LULUS |
| 7 | Mahasiswa | Lihat ringkasan pengajuan | Nama lab, jadwal, alat muncul | LULUS |
| 8 | Mahasiswa | Cek riwayat pribadi | Hanya booking sendiri | LULUS |
| 9 | Dosen | Login dan ajukan | Sama seperti mahasiswa | LULUS |
| 10 | Laboran | Lihat semua pengajuan | Semua booking tampil | LULUS |
| 11 | Laboran | Approve pengajuan | Status Disetujui | LULUS |
| 12 | Laboran | Tolak pengajuan | Status Ditolak dan alasan | LULUS |
| 13 | Laboran | Catat serah-terima | Status Dipinjam | LULUS |
| 14 | Laboran | Catat pengembalian | Status Dikembalikan | LULUS |
| 15 | Kalab | Lihat dashboard staff | Sama seperti laboran | LULUS |
| 16 | Sistem | Anti double-booking | Pengajuan bentrok ditolak | LULUS |
| 17 | Sistem | Validasi durasi maksimal | Pengajuan lebih ditolak | LULUS |
| 18 | Sistem | Validasi stok alat | Qty lebih dari stok ditolak | LULUS |
| 19 | Sistem | Keterlambatan otomatis | menit_telat terhitung | LULUS |
| 20 | Sistem | Waktu mulai masa depan | Waktu lampau ditolak | LULUS |

## Kesimpulan

Semua 20 skenario UAT LULUS. Sistem siap digunakan sesuai kebutuhan di SRS.
