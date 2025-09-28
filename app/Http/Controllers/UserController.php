<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Permintaan;
use App\Models\Pengiriman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;


class UserController extends Controller
{
    public function index(Request $request)
    {
        // Mapping role => label
        $roleLabels = [
            '1' => 'Superadmin',
            '2' => 'Regional Office Head',
            '3' => 'Warehouse Head',
            '4' => 'Field Technician',
        ];

        // Ambil role yang ada di DB
        $rolesFromDb = User::select('role')->distinct()->pluck('role')->toArray();

        // Buat array roles berdasarkan data dari DB
        $roles = collect($roleLabels)->only($rolesFromDb)->toArray();

        // Mulai query untuk ambil data user
        $query = User::query();

        // Terapkan filter role jika ada
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Ambil daftar region yang valid dan tidak kosong
        $regions = User::select('region')
            ->distinct()
            ->whereNotNull('region')  // Menghindari region kosong
            ->pluck('region');

        // Terapkan filter region jika ada
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        // Terapkan filter search jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Ambil data user dengan filter yang diterapkan
        $users = $query
            ->orderBy('role', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();  // Menjaga query string di URL

        // Kirim data ke view
        return view('kepalagudang.datauser', compact('users', 'roles', 'regions', 'roleLabels'));
    }


    public function store(Request $request)
    {
        // Validasi dan simpan data user baru
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|numeric|in:1,2,3,4',
            'bagian' => 'nullable|string|max:255',
            'atasan' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'region' => $request->region,
            'bagian' => $request->bagian,
            'atasan' => $request->atasan,
        ]);

        return redirect()->route('kepalagudang.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('kepalagudang.datauser', compact('user'));
    }

    public function update(Request $request, $id)
    {
        // Validasi data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|numeric|in:1,2,3,4',
            'region' => 'nullable|string|max:255',
            'atasan' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Temukan user berdasarkan ID
        $user = User::findOrFail($id);

        // Data yang akan diupdate
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'region' => $request->region,
            'atasan' => $request->atasan,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return response()->json(['success' => true]);

    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('kepalagudang.user.index')->with('success', 'User berhasil dihapus.');
    }


public function validasiIndex()
{
    $user = auth()->user();

    $requests = Permintaan::with(['details', 'pengiriman.details'])
        ->where('user_id', $user->id)
        ->where('status_gudang', 'approved')
        ->where('status_penerimaan', '!=', 'diterima')
        ->orderBy('tanggal_permintaan', 'desc')
        ->get();

    $data = $requests; // tambahkan ini
    return view('user.validasi', compact('requests', 'data'));
}


    public function terimaBarang(Request $request, $tiket)
    {
        $request->validate([
            'nomor_resi' => 'required|string|max:255',
            'foto_bukti' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $permintaan = Permintaan::where('tiket', $tiket)->firstOrFail();

        // Simpan foto
        $path = $request->file('foto_bukti')->store('bukti_penerimaan', 'public');

        // Update status
        $permintaan->update([
            'status_penerimaan' => 'diterima',
            'nomor_resi' => $request->nomor_resi,
            'foto_bukti_penerimaan' => $path,
            'tanggal_penerimaan' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil dikonfirmasi diterima!'
        ]);
    }

    public function historyIndex(Request $request)
    {
        $user = auth()->user();

       $query = Permintaan::with([
    'details',
    'pengiriman' => function ($q) {
        $q->select('id', 'tiket_permintaan', 'status') // pakai tiket_permintaan sebagai foreign key
          ->whereIn('status', ['diterima', 'close']);
    },
    'pengiriman.details',
])
->where('user_id', $user->id)
->whereHas('pengiriman', function (Builder $q) {
    $q->whereIn('status', ['diterima', 'close']);
})
->orderBy('tanggal_permintaan', 'desc');

        // Filter berdasarkan status
        if ($request->filled('statusFilter')) {
            $status = $request->statusFilter;
            if ($status === 'close') {
                $query->pengiriman->where('status', '!=', 'close');
            } elseif ($status === 'diterima') {
                $query->pengiriman->where('status', '!=', 'diterima');
            }
        }

        // Filter berdasarkan tanggal
        if ($request->filled('dateFilter')) {
            $query->whereDate('tanggal_permintaan', $request->dateFilter);
        }

        $requests = $query->get();

        return view('user.history', compact('requests'));
    }

        public function historyDetailApi($tiket)
    {
        $permintaan = Permintaan::with(['user', 'details'])
            ->where('tiket', $tiket)
            ->firstOrFail();

        $pengiriman = Pengiriman::with('details')
            ->where('tiket_permintaan', $tiket)
            ->first();

        return response()->json([
            'permintaan' => $permintaan,
            'pengiriman' => $pengiriman,
        ]);
    }
}