@extends('layouts.app')

@section('title', 'Detail Material')
@section('page-title', 'Detail Material')

@section('content')
<div class="mb-6">
    <a href="{{ route('materials.index') }}" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke List
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Material Info -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $material->material_name }}</h3>
                    <p class="text-gray-600">{{ $material->material_code }}</p>
                </div>
                <a href="{{ route('materials.edit', $material) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="border-b pb-3">
                    <p class="text-sm text-gray-600">Kategori</p>
                    <p class="font-semibold">{{ $material->category ?? '-' }}</p>
                </div>
                <div class="border-b pb-3">
                    <p class="text-sm text-gray-600">Unit of Measure</p>
                    <p class="font-semibold">{{ $material->unit_of_measure }}</p>
                </div>
                <div class="border-b pb-3">
                    <p class="text-sm text-gray-600">Current Stock</p>
                    <p class="font-semibold text-lg {{ $material->isLowStock() ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format($material->current_stock, 2) }}
                    </p>
                </div>
                <div class="border-b pb-3">
                    <p class="text-sm text-gray-600">Minimum Stock</p>
                    <p class="font-semibold">{{ number_format($material->minimum_stock, 2) }}</p>
                </div>
                <div class="border-b pb-3">
                    <p class="text-sm text-gray-600">Lokasi</p>
                    <p class="font-semibold">{{ $material->location ?? '-' }}</p>
                </div>
                <div class="border-b pb-3 col-span-2">
                    <p class="text-sm text-gray-600">Deskripsi</p>
                    <p class="font-semibold">{{ $material->description ?? '-' }}</p>
                </div>
                <div class="border-b pb-3">
                    <p class="text-sm text-gray-600">Status</p>
                    @if($material->is_active)
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Aktif</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">Nonaktif</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Status -->
    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="text-lg font-semibold mb-4">Stock Status</h4>
            @if($material->isLowStock())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    <span class="text-red-800 font-semibold">Stock Minimum!</span>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    <span class="text-green-800 font-semibold">Stock Aman</span>
                </div>
            @endif

            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Stock Level</span>
                        <span class="font-semibold">{{ $material->minimum_stock > 0 ? number_format(($material->current_stock / $material->minimum_stock) * 100, 0) : 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-{{ $material->isLowStock() ? 'red' : 'green' }}-600 h-2 rounded-full" 
                             style="width: {{ $material->minimum_stock > 0 ? min(($material->current_stock / $material->minimum_stock) * 100, 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Material Outputs -->
<div class="mt-6 bg-white rounded-lg shadow">
    <div class="p-6 border-b flex justify-between items-center">
        <h4 class="text-lg font-semibold">
            <i class="fas fa-boxes mr-2 text-green-600"></i>Material Output (Hasil dari 1 {{ $material->unit_of_measure }})
        </h4>
        <button onclick="toggleOutputForm()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i>Tambah Output
        </button>
    </div>

    <!-- Add Output Form (Hidden by default) -->
    <div id="outputForm" class="hidden p-6 border-b bg-gray-50">
        <form action="{{ route('material-outputs.store', $material) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Material Output</label>
                    <select name="output_material_id" id="materialOutputSelect" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Pilih Material</option>
                        @foreach($allMaterials->where('id', '!=', $material->id) as $mat)
                            <option value="{{ $mat->id }}">{{ $mat->material_code }} - {{ $mat->material_name }} ({{ $mat->unit_of_measure }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Output per {{ $material->unit_of_measure }}</label>
                    <input type="number" name="quantity_per_unit" step="0.01" min="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <input type="text" name="notes" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
                <button type="button" onclick="toggleOutputForm()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Batal
                </button>
            </div>
        </form>
    </div>

    <!-- Outputs List -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Material Output</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty per {{ $material->unit_of_measure }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($material->outputs as $output)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">
                        <div class="font-semibold">{{ $output->outputMaterial->material_name }}</div>
                        <div class="text-gray-600">{{ $output->outputMaterial->material_code }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-right">
                        <span class="font-bold text-lg">{{ number_format($output->quantity_per_unit, 2) }}</span> {{ $output->outputMaterial->unit_of_measure }}
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $output->notes ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($output->is_active)
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="toggleEditForm({{ $output->id }})" class="text-blue-600 hover:text-blue-800 mr-2" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('material-outputs.destroy', $output) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus output ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <!-- Edit Form Row (Hidden by default) -->
                <tr id="editForm{{ $output->id }}" class="hidden bg-blue-50">
                    <td colspan="5" class="px-6 py-4">
                        <form action="{{ route('material-outputs.update', $output) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Material Output</label>
                                    <input type="text" value="{{ $output->outputMaterial->material_name }}" class="w-full rounded-md border-gray-300 bg-gray-100" disabled>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Qty per {{ $material->unit_of_measure }}</label>
                                    <input type="number" name="quantity_per_unit" value="{{ $output->quantity_per_unit }}" step="0.01" min="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                    <input type="text" name="notes" value="{{ $output->notes }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_active" {{ $output->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm">Aktif</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mt-4 flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    <i class="fas fa-save mr-2"></i>Update
                                </button>
                                <button type="button" onclick="toggleEditForm({{ $output->id }})" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        Belum ada output material. Tambahkan output untuk menentukan material apa saja yang dihasilkan dari material ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($material->outputs->count() > 0)
    <div class="p-6 bg-gray-50 border-t">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 mr-2 mt-1"></i>
            <div>
                <p class="font-semibold text-gray-800">Contoh Kalkulasi:</p>
                <p class="text-sm text-gray-600">Dengan stock saat ini: <b>{{ number_format($material->current_stock, 2) }} {{ $material->unit_of_measure }}</b>, material ini dapat menghasilkan:</p>
                <ul class="mt-2 space-y-1">
                    @foreach($material->outputs->where('is_active', true) as $output)
                    <li class="text-sm text-gray-700">
                        <b>{{ number_format($material->current_stock * $output->quantity_per_unit, 2) }} {{ $output->outputMaterial->unit_of_measure }}</b> 
                        {{ $output->outputMaterial->material_name }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Transaction History -->
<div class="mt-6 bg-white rounded-lg shadow">
    <div class="p-6 border-b">
        <h4 class="text-lg font-semibold">
            <i class="fas fa-history mr-2 text-blue-600"></i>Transaction History
        </h4>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Transaksi</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm font-medium">{{ $transaction->transaction_number }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($transaction->transaction_type == 'IN')
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">IN</span>
                        @elseif($transaction->transaction_type == 'OUT')
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">OUT</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">ADJ</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-right font-semibold">{{ number_format($transaction->quantity, 2) }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($transaction->supplier)
                            {{ $transaction->supplier->supplier_name }}
                        @elseif($transaction->customer)
                            {{ $transaction->customer->customer_name }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $transaction->user->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada transaksi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 bg-gray-50">
        {{ $transactions->onEachSide(2)->links() }}
    </div>
</div>

<script>
function toggleOutputForm() {
    const form = document.getElementById('outputForm');
    form.classList.toggle('hidden');
    
    // Initialize or destroy Choices.js when form is toggled
    if (!form.classList.contains('hidden')) {
        initializeChoices();
    }
}

function toggleEditForm(id) {
    const form = document.getElementById('editForm' + id);
    form.classList.toggle('hidden');
}
</script>

<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">

<!-- Choices.js JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

<script>
// Initialize Choices.js for searchable select
let choicesInstance = null;

function initializeChoices() {
    const selectElement = document.getElementById('materialOutputSelect');
    if (selectElement && !choicesInstance) {
        choicesInstance = new Choices(selectElement, {
            searchEnabled: true,
            searchPlaceholderValue: 'Ketik untuk mencari...',
            noResultsText: 'Tidak ada hasil ditemukan',
            itemSelectText: 'Klik untuk pilih',
            removeItemButton: false,
            shouldSort: false,
            position: 'bottom',
        });
    }
}
</script>
@endsection
