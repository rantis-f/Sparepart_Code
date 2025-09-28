@extends('layouts.user')

@section('title', 'Daftar Jenis Barang')

@section('content')
    <div class="py-8 px-6">
        <!-- Filter -->
        <div class="mb-6 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                <select id="filter-kategori" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Semua</option>
                    <option value="aset" {{ request('kategori') == 'aset' ? 'selected' : '' }}>Aset</option>
                    <option value="non-aset" {{ request('kategori') == 'non-aset' ? 'selected' : '' }}>Non-Aset</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select id="filter-status" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Semua</option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
        </div>

        <!-- Tabel -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-auto w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Nama Barang
                            </th>
                            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @forelse ($jenisBarang as $jenis)
                            @foreach ($jenis->listBarang as $barang)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-2">{{ $no++ }}</td> <!-- Increment manual -->
                                    <td class="px-3 py-2">{{ $barang->jenisBarang?->nama ?? '-' }}
                                        {{ $barang->tipeBarang?->nama ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="
                                                            px-2 py-1 text-xs font-medium rounded-full
                                                            {{ $barang->kategori === 'aset' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}
                                                        ">
                                            {{ ucfirst($barang->kategori) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="
                                                            px-2 py-1 text-xs font-medium rounded-full
                                                            {{ $barang->quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}
                                                        ">
                                            {{ $barang->quantity > 0 ? 'Tersedia' : 'Habis' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox fa-2x text-gray-400 block mb-2"></i>
                                    Tidak ada data ditemukan.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script untuk Filter -->
    <script>
        document.getElementById('filter-kategori').addEventListener('change', function () {
            const kategori = this.value;
            const url = new URL(window.location.href.split('?')[0], window.location.origin);

            if (kategori) {
                url.searchParams.set('kategori', kategori);
            } else {
                url.searchParams.delete('kategori');
            }

            window.location.href = url.toString();
        });
        document.getElementById('filter-status').addEventListener('change', function () {
            const status = this.value;
            const url = new URL(window.location.href.split('?')[0], window.location.origin);

            // Pertahankan filter kategori jika ada
            const kategori = document.getElementById('filter-kategori').value;
            if (kategori) url.searchParams.set('kategori', kategori);

            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }

            window.location.href = url.toString();
        });
    </script>
@endsection