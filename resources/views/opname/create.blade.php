@extends('layouts.app')

@section('title', 'Stock Opname Baru')
@section('page-title', 'Buat Stock Opname Baru')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('opname.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Opname *</label>
                    <input type="date" name="opname_date" value="{{ old('opname_date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('opname_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse</label>
                    <select name="warehouse_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Warehouse</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Material *</label>
                    <select name="material_id" id="material_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Material</option>
                        @foreach($materials as $material)
                            <option value="{{ $material->id }}" data-stock="{{ $material->current_stock }}" data-uom="{{ $material->unit_of_measure }}">
                                {{ $material->material_code }} - {{ $material->material_name }} (Stock: {{ number_format($material->current_stock, 2) }} {{ $material->unit_of_measure }})
                            </option>
                        @endforeach
                    </select>
                    @error('material_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2 bg-blue-50 p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">System Stock</p>
                            <p class="text-2xl font-bold text-blue-600" id="system_stock">0.00</p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Physical Stock *</label>
                    <input type="number" name="physical_stock" value="{{ old('physical_stock') }}" step="0.01" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('physical_stock')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('opname.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Simpan Opname
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
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
    // Initialize Choices.js for searchable material select
    const materialSelect = document.getElementById('material_id');
    if (materialSelect) {
        const choices = new Choices(materialSelect, {
            searchEnabled: true,
            searchPlaceholderValue: 'Ketik untuk mencari...',
            noResultsText: 'Tidak ada hasil ditemukan',
            itemSelectText: 'Klik untuk pilih',
            removeItemButton: false,
            shouldSort: false,
            position: 'bottom',
        });
        
        // Handle change event for updating system stock
        materialSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock') || '0.00';
            const uom = selectedOption.getAttribute('data-uom') || '';
            
            document.getElementById('system_stock').textContent = parseFloat(stock).toFixed(2) + ' ' + uom;
        });
    }
</script>
@endpush
@endsection
