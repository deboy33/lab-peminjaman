@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="text-slate-500 text-sm mt-1">
                @if ($isStaff)
                    Daftar semua pengajuan peminjaman laboratorium.
                @else
                    Riwayat peminjaman laboratorium Anda.
                @endif
            </p>
        </div>
        <a href="{{ route('booking.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-lg shadow-indigo-600/20 transition">
            + Ajukan Peminjaman
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 ring-1 ring-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @foreach ([
            ['Total', $stats['total'], 'from-slate-500 to-slate-700'],
            ['Menunggu', $stats['menunggu'], 'from-amber-500 to-amber-700'],
            ['Disetujui', $stats['disetujui'], 'from-emerald-500 to-emerald-700'],
            ['Selesai', $stats['selesai'], 'from-sky-500 to-sky-700'],
        ] as $c)
            <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-5 shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $c[0] }}</span>
                <div class="mt-2 text-3xl font-bold text-slate-900">{{ $c[1] }}</div>
                <div class="mt-2 h-1 rounded-full bg-gradient-to-r {{ $c[2] }}"></div>
            </div>
        @endforeach
    </div>

    <div class="rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800">Riwayat Peminjaman</h2>
            <span class="text-xs text-slate-400">{{ $bookings->count() }} data</span>
        </div>

        @if ($bookings->isEmpty())
            <div class="py-16 text-center">
                <p class="text-slate-500">Belum ada pengajuan peminjaman.</p>
                <a href="{{ route('booking.create') }}" class="mt-4 inline-block text-indigo-600 font-semibold text-sm hover:underline">Ajukan sekarang →</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold">Kode</th>
                            @if ($isStaff)
                                <th class="text-left px-6 py-3 font-semibold">Pemohon</th>
                            @endif
                            <th class="text-left px-6 py-3 font-semibold">Lab</th>
                            <th class="text-left px-6 py-3 font-semibold">Alat</th>
                            <th class="text-left px-6 py-3 font-semibold">Jadwal</th>
                            <th class="text-left px-6 py-3 font-semibold">Status</th>
                            <th class="text-right px-6 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($bookings as $b)
                            @php [$label, $badge] = $b->status_badge; @endphp
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-6 py-4 font-mono text-xs text-slate-500">#{{ $b->id }}</td>
                                @if ($isStaff)
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-800">{{ $b->user?->name ?? '-' }}</div>
                                        <div class="text-xs text-slate-400">{{ ucfirst($b->user?->role ?? '') }}</div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 font-medium text-slate-800">{{ $b->lab?->nama ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @forelse ($b->equipments as $eq)
                                        <span class="inline-flex items-center gap-1 mr-1 mb-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-xs">
                                            {{ $eq->nama }} <span class="font-semibold">×{{ $eq->pivot->qty }}</span>
                                        </span>
                                    @empty
                                        <span class="text-slate-400">-</span>
                                    @endforelse
                                </td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">{{ $b->mulai?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 {{ $badge }}">{{ $label }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('booking.show', $b) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold text-xs">Detail →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
