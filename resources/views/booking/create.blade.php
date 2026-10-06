@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4" x-data="bookingForm()">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Ajukan Peminjaman</h1>
        <p class="text-slate-500 text-sm mt-1">Isi form di bawah untuk mengajukan peminjaman laboratorium.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-rose-50 ring-1 ring-rose-200 text-rose-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('booking.store') }}" class="grid md:grid-cols-3 gap-6">
        @csrf

        <div class="md:col-span-2 space-y-6">

            <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-6 shadow-sm">
                <label class="block font-semibold text-slate-800 mb-3">1. Pilih Laboratorium</label>
                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach ($labs as $lab)
                        <label class="cursor-pointer">
                            <input type="radio" name="lab_id" value="{{ $lab->id }}" x-model="labId"
                                   @change="rows = [{ id: '', qty: 1 }]" class="peer sr-only" required>
                            <div class="rounded-xl ring-1 ring-slate-200 p-4 peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:bg-indigo-50/40 hover:bg-slate-50 transition">
                                <div class="font-semibold text-slate-800">{{ $lab->nama }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $lab->lokasi ?? '-' }}</div>
                                <div class="text-xs text-slate-400 mt-1">
                                    {{ $lab->jam_buka ?? '08:00' }}–{{ $lab->jam_tutup ?? '17:00' }}
                                    @if($lab->max_durasi_jam) · maks {{ $lab->max_durasi_jam }} jam @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-6 shadow-sm">
                <label class="block font-semibold text-slate-800 mb-3">2. Jadwal Peminjaman</label>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase">Mulai</label>
                        <input type="datetime-local" name="mulai" x-model="mulai" required
                               class="mt-1 w-full rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase">Selesai</label>
                        <input type="datetime-local" name="selesai" x-model="selesai" required
                               class="mt-1 w-full rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-3" x-show="durasiText" x-text="durasiText"></p>
            </div>

            <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <label class="font-semibold text-slate-800">3. Alat yang Dipinjam</label>
                    <button type="button" @click="addRow()"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                        + Tambah Alat
                    </button>
                </div>

                <template x-if="!labId">
                    <p class="text-sm text-slate-400 py-4 text-center">Pilih lab dulu untuk melihat daftar alat.</p>
                </template>

                <template x-if="labId && availableEquipment().length === 0">
                    <p class="text-sm text-slate-400 py-4 text-center">Lab ini belum punya alat yang tersedia.</p>
                </template>

                <div class="space-y-2" x-show="labId && availableEquipment().length > 0">
                    <template x-for="(row, idx) in rows" :key="idx">
                        <div class="flex gap-2 items-start">
                            <select :name="`equipments[${idx}][id]`" x-model="row.id" required
                                    class="flex-1 rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="">— Pilih alat —</option>
                                <template x-for="eq in availableEquipment()" :key="eq.id">
                                    <option :value="eq.id" x-text="`${eq.nama} (stok: ${eq.stok})`"></option>
                                </template>
                            </select>
                            <input type="number" :name="`equipments[${idx}][qty]`" x-model.number="row.qty" min="1" :max="maxQty(row.id)"
                                   class="w-20 rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" required>
                            <button type="button" @click="rows.splice(idx, 1)"
                                    class="px-2.5 py-2 rounded-lg text-rose-600 hover:bg-rose-50 ring-1 ring-rose-200 text-sm">×</button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-6 shadow-sm">
                <label class="block font-semibold text-slate-800 mb-3">4. Keperluan</label>
                <textarea name="keperluan" rows="3" required maxlength="500" x-model="keperluan"
                          placeholder="Contoh: Praktikum konfigurasi routing untuk mata kuliah Jaringan Komputer"
                          class="w-full rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>
        </div>

        <div class="md:col-span-1">
            <div class="rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 text-white p-6 shadow-lg sticky top-6">
                <h3 class="font-semibold mb-4">Ringkasan</h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <div class="text-indigo-200 text-xs uppercase">Lab</div>
                        <div class="font-semibold" x-text="labName()"></div>
                    </div>
                    <div>
                        <div class="text-indigo-200 text-xs uppercase">Jadwal</div>
                        <div class="font-semibold text-xs" x-text="jadwalText()"></div>
                    </div>
                    <div>
                        <div class="text-indigo-200 text-xs uppercase">Alat</div>
                        <div class="font-semibold">
                            <span x-text="rows.filter(r => r.id).length"></span> item
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="mt-6 w-full rounded-xl bg-white text-indigo-700 hover:bg-indigo-50 font-semibold py-3 transition">
                    Ajukan Peminjaman
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function bookingForm() {
    return {
        labId: '',
        mulai: '',
        selesai: '',
        keperluan: '',
        allEquipment: @json($equipmentList),
        allLabs: @json($labList),
        rows: [{ id: '', qty: 1 }],

        addRow() { this.rows.push({ id: '', qty: 1 }); },

        availableEquipment() {
            if (!this.labId) return [];
            return this.allEquipment.filter(e => e.lab_id == this.labId);
        },

        maxQty(id) {
            const eq = this.availableEquipment().find(e => e.id == id);
            return eq ? eq.stok : 1;
        },

        labName() {
            if (!this.labId) return '—';
            const lab = this.allLabs.find(l => l.id == this.labId);
            return lab ? lab.nama : '—';
        },

        jadwalText() {
            if (!this.mulai || !this.selesai) return '—';
            return `${this.mulai.replace('T', ' ')} → ${this.selesai.replace('T', ' ')}`;
        },

        get durasiText() {
            if (!this.mulai || !this.selesai) return '';
            const ms = new Date(this.selesai) - new Date(this.mulai);
            if (ms <= 0) return '⚠️ Waktu selesai harus setelah waktu mulai';
            const j = Math.floor(ms / 3600000);
            const m = Math.floor((ms % 3600000) / 60000);
            return `Durasi: ${j} jam ${m} menit`;
        },
    };
}
</script>
@endsection
