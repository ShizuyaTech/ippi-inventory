@extends('layouts.app')

@section('title', 'Edit Material')
@section('page-title', 'Edit Material')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('materials.update', $material) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Material *</label>
                    <input type="text" name="material_code" value="{{ old('material_code', $material->material_code) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('material_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Material *</label>
                    <input type="text" name="material_name" value="{{ old('material_name', $material->material_name) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('material_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $material->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit of Measure *</label>
                    <select name="unit_of_measure" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih UOM</option>
                        <option value="PCS" {{ old('unit_of_measure', $material->unit_of_measure) == 'PCS' ? 'selected' : '' }}>PCS (Pieces)</option>
                        <option value="KG" {{ old('unit_of_measure', $material->unit_of_measure) == 'KG' ? 'selected' : '' }}>KG (Kilogram)</option>
                        <option value="TON" {{ old('unit_of_measure', $material->unit_of_measure) == 'TON' ? 'selected' : '' }}>TON</option>
                        <option value="M" {{ old('unit_of_measure', $material->unit_of_measure) == 'M' ? 'selected' : '' }}>M (Meter)</option>
                        <option value="M2" {{ old('unit_of_measure', $material->unit_of_measure) == 'M2' ? 'selected' : '' }}>M2 (Square Meter)</option>
                        <option value="ROLL" {{ old('unit_of_measure', $material->unit_of_measure) == 'ROLL' ? 'selected' : '' }}>ROLL</option>
                        <option value="BOX" {{ old('unit_of_measure', $material->unit_of_measure) == 'BOX' ? 'selected' : '' }}>BOX</option>
                        <option value="SHEET" {{ old('unit_of_measure', $material->unit_of_measure) == 'SHEET' ? 'selected' : '' }}>SHEET (Lembaran)</option>
                    </select>
                    @error('unit_of_measure')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Kategori</option>
                        <option value="Raw Material" {{ old('category', $material->category) == 'Raw Material' ? 'selected' : '' }}>Raw Material</option>
                        <option value="WIP" {{ old('category', $material->category) == 'WIP' ? 'selected' : '' }}>WIP (Work In Process)</option>
                        <option value="Finished Goods" {{ old('category', $material->category) == 'Finished Goods' ? 'selected' : '' }}>Finished Goods</option>
                        <option value="Consumables" {{ old('category', $material->category) == 'Consumables' ? 'selected' : '' }}>Consumables</option>
                        <option value="Tools" {{ old('category', $material->category) == 'Tools' ? 'selected' : '' }}>Tools</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock *</label>
                    <input type="number" name="minimum_stock" value="{{ old('minimum_stock', $material->minimum_stock) }}" step="0.01" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('minimum_stock')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $material->location) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $material->is_active) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('materials.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
            </div>
        </form>
    </div>

    <!-- Material Outputs Management -->
    <div class="mt-6 bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <h4 class="text-lg font-semibold">
                <i class="fas fa-boxes mr-2 text-green-600"></i>Material Output (Hasil dari 1 {{ $material->unit_of_measure }})
            </h4>
            <button onclick="toggleOutputForm()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
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
                        <input type="number" name="quantity_per_unit" step="0.01" min="0.01" class="w-full px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <input type="text" name="notes" class="w-full px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                    <button type="button" onclick="toggleOutputForm()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
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
                                            <input type="checkbox" name="is_active" value="1" {{ $output->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm">Aktif</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-4 flex gap-2">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                        <i class="fas fa-save mr-2"></i>Update
                                    </button>
                                    <button type="button" onclick="toggleEditForm({{ $output->id }})" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
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

<!-- Custom CSS to match input heights -->
<style>
    .choices__inner {
        min-height: 42px !important;
        padding: 0.5rem 0.75rem !important;
        display: flex !important;
        align-items: center !important;
    }
    .choices__list--single {
        padding: 0 !important;
    }
</style>

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
