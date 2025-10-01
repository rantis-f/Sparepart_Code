<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Permintaan;
use App\Models\Pengiriman;
use App\Models\Region;
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

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
          if ($request->filled('region')) {
        $query->where('region', $request->region);
    }

        if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('email', 'like', "%{$searchTerm}%");
        });
    }

        $regions = Region::all();

        $users = $query
            ->orderBy('role', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); 

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
            'jabatan' => 'nullable|string|max:255',
            'atasan' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'region' => $request->region,
            'bagian' => $request->jabatan,
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
            'jabatan' => 'nullable|string|max:255',
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
            'bagian' => $request->jabatan,
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

        $requests = Permintaan::with(['details', 'pengiriman.details', 'pengiriman'])
            ->where('user_id', $user->id)
            ->where('status_gudang', 'approved')
            ->whereHas('pengiriman', function ($query) {
                $query->where('status', '!=', 'diterima');
            })
            ->orderBy('id', 'desc')
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
        $q->select('id', 'tiket_permintaan', 'status')
            ->whereIn('status', ['diterima', 'close']);
    },
    'pengiriman.details',
])
->where('user_id', $user->id)
->where(function (Builder $q) {
    $q->whereHas('pengiriman', function (Builder $q2) {
        $q2->whereIn('status', ['diterima', 'close']);
    })
   ->orWhere('status_super_admin', 'rejected');
})
->orderBy('id', 'desc');


        if ($request->filled('statusFilter')) {
    $status = $request->statusFilter;

    if (in_array($status, ['close', 'diterima', 'rejected'])) {
        $query->where(function ($q) use ($status) {
            $q->whereHas('pengiriman', function ($q2) use ($status) {
                $q2->where('status', $status);
            })
            ->orWhere('status_super_admin', $status);
        });
    }
}

    // Filter berdasarkan tanggal permintaan
    if ($request->filled('start_date')) {
        $query->whereDate('tanggal_permintaan', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $query->whereDate('tanggal_permintaan', '<=', $request->end_date);
    }

    $requests = $query->orderBy('id', 'desc')->get();

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
