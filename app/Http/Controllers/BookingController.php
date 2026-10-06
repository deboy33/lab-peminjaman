<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Lab;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isStaff = in_array($user->role, ['laboran', 'kalab']);

        $query = Booking::with(['lab', 'equipments', 'user']);

        if (! $isStaff) {
            $query->where('user_id', $user->id);
        }

        $bookings = $query->latest()->get();

        $stats = [
            'total'     => $bookings->count(),
            'menunggu'  => $bookings->where('status', 'menunggu')->count(),
            'disetujui' => $bookings->whereIn('status', ['disetujui', 'dipinjam'])->count(),
            'selesai'   => $bookings->where('status', 'dikembalikan')->count(),
        ];

        return view('booking.index', compact('bookings', 'stats', 'isStaff'));
    }

    public function create()
    {
        $labs = Lab::with(['equipments' => fn ($q) => $q->where('stok', '>', 0)])
                   ->orderBy('nama')->get();

        $equipmentList = Equipment::where('stok', '>', 0)
            ->get(['id', 'lab_id', 'nama', 'stok'])
            ->map(fn ($e) => [
                'id'     => $e->id,
                'lab_id' => $e->lab_id,
                'nama'   => $e->nama,
                'stok'   => $e->stok,
            ])
            ->values();

        $labList = $labs->map(fn ($l) => [
            'id'   => $l->id,
            'nama' => $l->nama,
        ])->values();

        return view('booking.create', compact('labs', 'equipmentList', 'labList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lab_id'            => ['required', 'exists:labs,id'],
            'mulai'             => ['required', 'date', 'after:now'],
            'selesai'           => ['required', 'date', 'after:mulai'],
            'keperluan'         => ['required', 'string', 'max:500'],
            'equipments'        => ['required', 'array', 'min:1'],
            'equipments.*.id'   => ['required', 'exists:equipment,id'],
            'equipments.*.qty'  => ['required', 'integer', 'min:1'],
        ], [
            'equipments.required' => 'Pilih minimal satu alat.',
            'mulai.after'         => 'Waktu mulai harus di masa depan.',
            'selesai.after'       => 'Waktu selesai harus setelah waktu mulai.',
        ]);

        $lab = Lab::findOrFail($data['lab_id']);
        $durasiJam = Carbon::parse($data['mulai'])->diffInHours(Carbon::parse($data['selesai']));

        // ─── Validasi durasi maksimal ───────────────────
        if ($lab->max_durasi_jam && $durasiJam > $lab->max_durasi_jam) {
            return back()->withErrors([
                'selesai' => "Durasi maksimal untuk {$lab->nama} adalah {$lab->max_durasi_jam} jam.",
            ])->withInput();
        }

        // ─── ANTI DOUBLE-BOOKING ────────────────────────
        $mulai   = Carbon::parse($data['mulai']);
        $selesai = Carbon::parse($data['selesai']);

        $bentrok = Booking::where('lab_id', $data['lab_id'])
            ->whereIn('status', ['menunggu', 'disetujui', 'dipinjam'])
            ->where(function ($q) use ($mulai, $selesai) {
                // Overlap jika: (mulai_existing < selesai_baru) AND (selesai_existing > mulai_baru)
                $q->where('mulai', '<', $selesai)
                  ->where('selesai', '>', $mulai);
            })
            ->exists();

        if ($bentrok) {
            return back()->withErrors([
                'selesai' => "Lab {$lab->nama} sudah dipesan di jam tersebut. Silakan pilih waktu lain.",
            ])->withInput();
        }

        // ─── Validasi stok alat ─────────────────────────
        foreach ($data['equipments'] as $i => $item) {
            $eq = Equipment::find($item['id']);
            if (! $eq || $item['qty'] > $eq->stok) {
                return back()->withErrors([
                    "equipments.$i.qty" => "Stok " . ($eq->nama ?? 'alat') . " hanya tersisa " . ($eq->stok ?? 0) . ".",
                ])->withInput();
            }
        }

        DB::transaction(function () use ($data) {
            $booking = Booking::create([
                'user_id'      => Auth::id(),
                'lab_id'       => $data['lab_id'],
                'equipment_id' => $data['equipments'][0]['id'],
                'mulai'        => $data['mulai'],
                'selesai'      => $data['selesai'],
                'keperluan'    => $data['keperluan'],
                'status'       => 'menunggu',
            ]);

            foreach ($data['equipments'] as $item) {
                $booking->equipments()->attach($item['id'], ['qty' => $item['qty']]);
            }
        });

        return redirect()->route('dashboard')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }

    public function show(Booking $booking)
    {
        $user = Auth::user();
        $isStaff = in_array($user->role, ['laboran', 'kalab']);

        abort_unless($booking->user_id === $user->id || $isStaff, 403);

        $booking->load(['lab', 'equipments', 'user', 'diprosesOleh']);

        return view('booking.show', compact('booking', 'isStaff'));
    }

    public function approve(Booking $booking)
    {
        abort_unless(Auth::user()->isLaboran() || Auth::user()->isKalab(), 403);
        abort_unless($booking->status === 'menunggu', 400, 'Pengajuan sudah diproses.');

        $booking->update([
            'status'        => 'disetujui',
            'diproses_oleh' => Auth::id(),
            'diproses_pada' => now(),
        ]);

        return back()->with('success', "Pengajuan #{$booking->id} disetujui.");
    }

    public function reject(Request $request, Booking $booking)
    {
        abort_unless(Auth::user()->isLaboran() || Auth::user()->isKalab(), 403);
        abort_unless($booking->status === 'menunggu', 400, 'Pengajuan sudah diproses.');

        $data = $request->validate([
            'alasan_tolak' => ['required', 'string', 'max:500'],
        ]);

        $booking->update([
            'status'        => 'ditolak',
            'alasan_tolak'  => $data['alasan_tolak'],
            'diproses_oleh' => Auth::id(),
            'diproses_pada' => now(),
        ]);

        return back()->with('success', "Pengajuan #{$booking->id} ditolak.");
    }

    public function handover(Booking $booking)
    {
        abort_unless(Auth::user()->isLaboran() || Auth::user()->isKalab(), 403);
        abort_unless($booking->status === 'disetujui', 400, 'Status tidak valid untuk serah-terima.');

        $booking->update([
            'status'       => 'dipinjam',
            'diambil_pada' => now(),
        ]);

        return back()->with('success', "Serah-terima #{$booking->id} dicatat.");
    }

    public function returnBack(Request $request, Booking $booking)
    {
        abort_unless(Auth::user()->isLaboran() || Auth::user()->isKalab(), 403);
        abort_unless($booking->status === 'dipinjam', 400, 'Status tidak valid untuk pengembalian.');

        $data = $request->validate([
            'kondisi_kembali'  => ['required', 'in:baik,rusak_ringan,rusak_berat,hilang'],
            'catatan_kembali'  => ['nullable', 'string', 'max:500'],
        ]);

        $menitTelat = 0;
        if ($booking->selesai && now()->greaterThan($booking->selesai)) {
            $menitTelat = $booking->selesai->diffInMinutes(now());
        }

        $booking->update([
            'status'            => 'dikembalikan',
            'dikembalikan_pada' => now(),
            'kondisi_kembali'   => $data['kondisi_kembali'],
            'catatan_kembali'   => $data['catatan_kembali'] ?? null,
            'menit_telat'       => $menitTelat,
        ]);

        return back()->with('success', "Pengembalian #{$booking->id} dicatat.");
    }
}
