@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:underline">← Kembali ke Dashboard</a>
            <h1 class="text-2xl font-bold text-slate-900 mt-2">Detail Peminjaman #{{ $booking->id }}</h1>
        </div>
        @php [$label, $badge] = $booking->status_badge; @endphp
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold ring-1 {{ $badge }}">{{ $label }}</span>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 ring-1 ring-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-rose-50 ring-1 ring-rose-200 text-rose-800 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- PANEL AKSI (staff only) --}}
    @if ($isStaff)
        <div class="mb-6 rounded-2xl bg-indigo-50 ring-1 ring-indigo-200 p-6">
            <h2 class="font-semibold text-indigo-900 mb-4">Panel Aksi Laboran</h2>

            @if ($booking->status === 'menunggu')
                <div class="flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('booking.approve', $booking) }}">
                        @csrf
                        <button class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-2.5">
                            ✓ Setujui
                        </button>
                    </form>

                    <button type="button" onclick="document.getElementById('rejectForm').classList.toggle('hidden')"
                            class="rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold px-5 py-2.5">
                        ✗ Tolak
                    </button>
                </div>

                <form id="rejectForm" method="POST" action="{{ route('booking.reject', $booking) }}" class="hidden mt-4 space-y-3">
                    @csrf
                    <textarea name="alasan_tolak" rows="2" required maxlength="500"
                              placeholder="Alasan penolakan (wajib)..."
                              class="w-full rounded-lg ring-1 ring-rose-200 px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500 outline-none"></textarea>
                    <button class="rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold px-4 py-2">
                        Kirim Penolakan
                    </button>
                </form>
            @elseif ($booking->status === 'disetujui')
                <form method="POST" action="{{ route('booking.handover', $booking) }}">
                    @csrf
                    <button class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold px-5 py-2.5">
                        📦 Catat Serah-Terima (Alat Diambil)
                    </button>
                </form>
            @elseif ($booking->status === 'dipinjam')
                <form method="POST" action="{{ route('booking.return', $booking) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kondisi Alat Saat Kembali</label>
                        <select name="kondisi_kembali" required
                                class="w-full rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">— Pilih kondisi —</option>
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                            <option value="hilang">Hilang</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Catatan (opsional)</label>
                        <textarea name="catatan_kembali" rows="2" maxlength="500"
                                  placeholder="Catatan tambahan..."
                                  class="w-full rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                    </div>
                    <button class="rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold px-5 py-2.5">
                        📥 Catat Pengembalian
                    </button>
                </form>
            @else
                <p class="text-sm text-indigo-700">Tidak ada aksi tersedia untuk status <strong>{{ $booking->status }}</strong>.</p>
            @endif
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">

        <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-6 shadow-sm">
            <h2 class="font-semibold text-slate-800 mb-4">Informasi Peminjaman</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Pemohon</dt>
                    <dd class="font-medium text-slate-800">{{ $booking->user?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Laboratorium</dt>
                    <dd class="font-medium text-slate-800">{{ $booking->lab?->nama ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Mulai</dt>
                    <dd class="font-medium text-slate-800">{{ $booking->mulai?->format('d/m/Y H:i') ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Selesai</dt>
                    <dd class="font-medium text-slate-800">{{ $booking->selesai?->format('d/m/Y H:i') ?? '-' }}</dd>
                </div>
                @if ($booking->diproses_pada)
                <div class="flex justify-between">
                    <dt class="text-slate-500">Diproses</dt>
                    <dd class="font-medium text-slate-800">{{ $booking->diproses_pada->format('d/m/Y H:i') }}</dd>
                </div>
                @endif
                @if ($booking->diprosesOleh)
                <div class="flex justify-between">
                    <dt class="text-slate-500">Oleh</dt>
                    <dd class="font-medium text-slate-800">{{ $booking->diprosesOleh->name }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-6 shadow-sm">
            <h2 class="font-semibold text-slate-800 mb-4">Alat yang Dipinjam</h2>
            @if ($booking->equipments->isEmpty())
                <p class="text-sm text-slate-400">Tidak ada alat.</p>
            @else
                <ul class="space-y-2">
                    @foreach ($booking->equipments as $eq)
                        <li class="flex items-center justify-between text-sm py-2 border-b border-slate-100 last:border-0">
                            <span class="text-slate-700">{{ $eq->nama }}</span>
                            <span class="font-semibold text-slate-800">×{{ $eq->pivot->qty }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="md:col-span-2 rounded-2xl bg-white ring-1 ring-slate-200 p-6 shadow-sm">
            <h2 class="font-semibold text-slate-800 mb-3">Keperluan</h2>
            <p class="text-sm text-slate-600 leading-relaxed">{{ $booking->keperluan ?? '-' }}</p>
        </div>

        @if ($booking->alasan_tolak)
        <div class="md:col-span-2 rounded-2xl bg-rose-50 ring-1 ring-rose-200 p-6">
            <h2 class="font-semibold text-rose-800 mb-2">Alasan Ditolak</h2>
            <p class="text-sm text-rose-700">{{ $booking->alasan_tolak }}</p>
        </div>
        @endif

        @if ($booking->kondisi_kembali || $booking->catatan_kembali || $booking->menit_telat)
        <div class="md:col-span-2 rounded-2xl bg-slate-50 ring-1 ring-slate-200 p-6">
            <h2 class="font-semibold text-slate-800 mb-3">Pengembalian</h2>
            <dl class="space-y-2 text-sm">
                @if ($booking->dikembalikan_pada)
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Dikembalikan</dt>
                        <dd class="font-medium text-slate-800">{{ $booking->dikembalikan_pada->format('d/m/Y H:i') }}</dd>
                    </div>
                @endif
                @if ($booking->kondisi_kembali)
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Kondisi</dt>
                        <dd class="font-medium text-slate-800">{{ ucfirst(str_replace('_', ' ', $booking->kondisi_kembali)) }}</dd>
                    </div>
                @endif
                @if ($booking->menit_telat)
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Keterlambatan</dt>
                        <dd class="font-medium text-rose-700">{{ $booking->menit_telat }} menit</dd>
                    </div>
                @endif
                @if ($booking->catatan_kembali)
                    <div>
                        <dt class="text-slate-500 mb-1">Catatan</dt>
                        <dd class="text-slate-700">{{ $booking->catatan_kembali }}</dd>
                    </div>
                @endif
            </dl>
        </div>
        @endif
    </div>
</div>
@endsection
