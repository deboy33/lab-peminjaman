# UI/UX Guide

**Sistem Peminjaman Laboratorium**
**Kelompok 3 — PPL**

## Design System

### Color Palette

| Peran | Warna | Tailwind Class |
|-------|-------|----------------|
| Primary | Indigo | indigo-600, indigo-700 |
| Accent Ringkasan | Gradient | from-indigo-600 to-purple-700 |
| Success | Emerald | emerald-600, emerald-50 |
| Danger | Rose | rose-600, rose-50 |
| Warning | Amber | amber-100, amber-800 |
| Info | Sky | sky-600, sky-100 |
| Background | Slate | slate-50 |
| Text Primary | Slate | slate-900 |
| Text Secondary | Slate | slate-500 |
| Border | Slate | slate-200 |

### Typography

- Font: Default Tailwind (system font stack)
- Heading 1: text-2xl font-bold text-slate-900
- Heading 2: font-semibold text-slate-800
- Body: text-sm text-slate-600
- Label: text-xs font-semibold text-slate-500 uppercase

### Spacing

- Container: max-w-6xl mx-auto py-8 px-4
- Card: rounded-2xl bg-white ring-1 ring-slate-200 p-6 shadow-sm
- Gap antar card: gap-6 atau gap-4

## Layout Patterns

### 1. Topbar
- Background: bg-slate-900 text-white
- Container: max-w-6xl mx-auto px-4 py-3
- Isi: Judul kiri + user info kanan

### 2. Card Grid (Pilih Lab)
- Grid: grid sm:grid-cols-2 gap-3
- Card: rounded-xl ring-1 ring-slate-200 p-4
- Active: peer-checked:ring-2 peer-checked:ring-indigo-500

### 3. Form Input
- Base: w-full rounded-lg ring-1 ring-slate-200 px-3 py-2.5
- Focus: focus:ring-2 focus:ring-indigo-500 outline-none

### 4. Tombol Primary
- px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-lg

### 5. Badge Status
- Format: inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1
- Warna disesuaikan status (menunggu, disetujui, ditolak, dipinjam, dikembalikan)

## Responsivitas

- Breakpoint: sm (640px), md (768px)
- Tabel di HP: overflow-x-auto, user swipe horizontal
- Card grid: 1 kolom di HP, 2 kolom di tablet ke atas
- Form: 1 kolom di HP, 2 kolom di tablet ke atas

## Komponen Khas

### Ringkasan Sticky
- Posisi: sticky top-6
- Background: bg-gradient-to-br from-indigo-600 to-purple-700
- Teks: text-white
- Update real-time pakai Alpine.js

### Panel Aksi Laboran
- Background: bg-indigo-50 ring-1 ring-indigo-200
- Tombol: Setujui (hijau), Tolak (merah)

## Status Badge (Warna)

| Status | Warna | Tailwind |
|--------|-------|----------|
| Menunggu | Kuning | bg-amber-100 text-amber-800 |
| Disetujui | Hijau | bg-emerald-100 text-emerald-800 |
| Ditolak | Merah | bg-rose-100 text-rose-800 |
| Dipinjam | Biru | bg-sky-100 text-sky-800 |
| Dikembalikan | Abu | bg-slate-100 text-slate-700 |

## Screenshot

Screenshot semua halaman tersedia di folder docs/screenshots/.
