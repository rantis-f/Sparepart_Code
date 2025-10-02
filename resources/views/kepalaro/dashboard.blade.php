@extends('layouts.kepalaro')

@section('title', 'Dashboard Kepala RO')

@section('content')
<div x-data="{
    showDetailModal: false,
    showApproveModal: false,
    showRejectModal: false,
    detailData: {},
    approveId: null,
    rejectId: null,

    openDetail(data) {
        this.detailData = data;
        this.showDetailModal = true;
    },
    confirmApprove(id) {
        this.approveId = id;
        this.showApproveModal = true;
    },
    confirmReject(id) {
        this.rejectId = id;
        this.showRejectModal = true;
    }
}">

    <div class="py-8 px-6">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <h2 class="text-2xl font-semibold mb-6 text-gray-800">Permintaan Menunggu Approval</h2>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemohon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($requests as $index => $req)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ $index + 1}}</td>
                            <td class="px-6 py-4 text-sm">{{ $req->user?->name ?? 'Tidak Diketahui' }}</td>
                            <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($req->tanggal_permintaan)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm space-x-3">
                                <button @click="openDetail({{ json_encode([
                                    'tiket' => $req->tiket,
                                    'user' => $req->user?->name,
                                    'email' => $req->user?->email,
                                    'tanggal' => \Carbon\Carbon::parse($req->tanggal_permintaan)->format('d M Y'),
                                    'details' => $req->details
                                ]) }})"
                                        class="text-blue-600 hover:text-blue-800 hover:underline text-sm font-medium">
                                    Detail
                                </button>
                                <button @click="confirmApprove({{ $req->id }})"
                                        class="text-green-600 hover:text-green-800 hover:underline text-sm font-medium">
                                    Approve
                                </button>
                                <button @click="confirmReject({{ $req->id }})"
                                        class="text-red-600 hover:text-red-800 hover:underline text-sm font-medium">
                                    Reject
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada permintaan menunggu approval.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Detail -->
    <div x-show="showDetailModal"
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         x-cloak>
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-blue-600 text-white p-4 rounded-t-lg flex justify-between items-center">
                <h3 class="text-lg font-semibold">Detail Request</h3>
                <button @click="showDetailModal = false" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Nama Tiket:</p>
                        <p class="font-medium" x-text="detailData.tiket"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">User:</p>
                        <p class="font-medium" x-text="detailData.user"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tanggal:</p>
                        <p class="font-medium" x-text="detailData.tanggal"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Jumlah Item:</p>
                        <p class="font-medium" x-text="detailData.details?.length || 0"></p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 text-sm">
                        <thead class="bg-blue-50 text-blue-700">
                            <tr>
                                <th class="px-4 py-2 border-b text-left">No</th>
                                <th class="px-4 py-2 border-b text-left">Nama Item</th>
                                <th class="px-4 py-2 border-b text-left">Deskripsi</th>
                                <th class="px-4 py-2 border-b text-left">Jumlah</th>
                                <th class="px-4 py-2 border-b text-left">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="detailData.details && detailData.details.length > 0">
                                <template x-for="(item, index) in detailData.details" :key="item.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border-b text-center" x-text="index + 1"></td>
                                        <td class="px-4 py-2 border-b" x-text="item.nama_item"></td>
                                        <td class="px-4 py-2 border-b" x-text="item.deskripsi"></td>
                                        <td class="px-4 py-2 border-b text-center" x-text="item.jumlah"></td>
                                        <td class="px-4 py-2 border-b" x-text="item.keterangan || '-'"></td>
                                    </tr>
                                </template>
                            </template>
                            <tr x-show="!detailData.details || detailData.details.length === 0">
                                <td colspan="5" class="px-4 py-2 text-center text-gray-500">Tidak ada item.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end">
                <button @click="showDetailModal = false"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded text-sm font-medium transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Approve -->
    <div x-show="showApproveModal"
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         x-cloak>
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <h3 class="text-lg font-bold">Setujui Permintaan?</h3>
            <p class="text-gray-600 text-sm">Apakah Anda yakin ingin menyetujui permintaan ini?</p>
            <form :action="`/kepalaro/approve/${approveId}`" method="POST">
                @csrf
                <div class="mt-6 text-right space-x-2">
                    <button type="button" @click="showApproveModal = false"
                            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm font-medium">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
                        Ya, Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Reject -->
    <div x-show="showRejectModal"
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         x-cloak>
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <h3 class="text-lg font-bold">Tolak Permintaan</h3>
            <form :action="`/kepalaro/reject/${rejectId}`" method="POST">
                @csrf
                <label class="block mt-3 text-sm font-medium text-gray-700">Catatan Penolakan</label>
                <textarea name="catatan" class="w-full border border-gray-300 rounded-lg p-2 mt-1 text-sm" 
                          placeholder="Jelaskan alasan penolakan..." rows="3" required></textarea>
                <div class="mt-6 text-right space-x-2">
                    <button type="button" @click="showRejectModal = false"
                            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm font-medium">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection