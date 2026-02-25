@extends('layouts.app')

@section('title', 'Transaksi Baru')
@section('page-title', 'Buat Transaksi Stock Baru')

@section('content')
<div class="max-w-6xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf
            
            <!-- Header Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 pb-6 border-b">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Transaksi *</label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('transaction_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Transaksi *</label>
                    <select name="transaction_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Tipe</option>
                        <option value="IN" {{ old('transaction_type') == 'IN' ? 'selected' : '' }}>Stock IN</option>
                        <option value="OUT" {{ old('transaction_type') == 'OUT' ? 'selected' : '' }}>Stock OUT</option>
                        <option value="ADJUSTMENT" {{ old('transaction_type') == 'ADJUSTMENT' ? 'selected' : '' }}>Adjustment</option>
                    </select>
                    @error('transaction_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Referensi (PO/DO)</label>
                    <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier (untuk Stock IN)</label>
                    <select name="supplier_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer (untuk Stock OUT)</label>
                    <select name="customer_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Multiple Items Section -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Items</h3>
                    <button type="button" onclick="addItemRow()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>Tambah Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-4 py-2 text-left">Material *</th>
                                <th class="border px-4 py-2 text-left" style="width: 150px;">Quantity *</th>
                                <th class="border px-4 py-2 text-left">Notes</th>
                                <th class="border px-4 py-2 text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Initial Row -->
                            <tr class="item-row">
                                <td class="border px-4 py-2">
                                    <select name="items[0][material_id]" class="material-select" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Pilih Material</option>
                                        @foreach($materials as $material)
                                            <option value="{{ $material->id }}">
                                                {{ $material->material_code }} - {{ $material->material_name }} ({{ number_format($material->current_stock, 2) }} {{ $material->unit_of_measure }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="border px-4 py-2">
                                    <input type="number" name="items[0][quantity]" step="0.01" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="border px-4 py-2">
                                    <input type="text" name="items[0][notes]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <button type="button" onclick="removeItemRow(this)" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Umum</label>
                <textarea name="notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('transactions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                    <i class="fas fa-save mr-2"></i>Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

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
let itemCounter = 1;
const materialsData = @json($materials);
const choicesInstances = [];

// Initialize Choices.js for initial row
document.addEventListener('DOMContentLoaded', function() {
    initializeChoicesForRow(0);
});

function initializeChoicesForRow(index) {
    const selects = document.querySelectorAll('.material-select');
    const selectElement = selects[index];
    
    if (selectElement && !selectElement.dataset.choicesInitialized) {
        const choices = new Choices(selectElement, {
            searchEnabled: true,
            searchPlaceholderValue: 'Ketik untuk mencari...',
            noResultsText: 'Tidak ada hasil ditemukan',
            itemSelectText: 'Klik untuk pilih',
            removeItemButton: false,
            shouldSort: false,
            position: 'bottom',
        });
        
        selectElement.dataset.choicesInitialized = 'true';
        choicesInstances.push({
            element: selectElement,
            instance: choices
        });
    }
}

function addItemRow() {
    const tbody = document.getElementById('itemsTableBody');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row';
    
    let materialsOptions = '<option value="">Pilih Material</option>';
    materialsData.forEach(material => {
        materialsOptions += `<option value="${material.id}">${material.material_code} - ${material.material_name} (${parseFloat(material.current_stock).toFixed(2)} ${material.unit_of_measure})</option>`;
    });
    
    newRow.innerHTML = `
        <td class="border px-4 py-2">
            <select name="items[${itemCounter}][material_id]" class="material-select" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                ${materialsOptions}
            </select>
        </td>
        <td class="border px-4 py-2">
            <input type="number" name="items[${itemCounter}][quantity]" step="0.01" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </td>
        <td class="border px-4 py-2">
            <input type="text" name="items[${itemCounter}][notes]"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </td>
        <td class="border px-4 py-2 text-center">
            <button type="button" onclick="removeItemRow(this)" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    itemCounter++;
    
    // Initialize Choices.js for the new row
    const allSelects = document.querySelectorAll('.material-select');
    initializeChoicesForRow(allSelects.length - 1);
}

function removeItemRow(button) {
    const tbody = document.getElementById('itemsTableBody');
    if (tbody.children.length > 1) {
        const row = button.closest('tr');
        const selectElement = row.querySelector('.material-select');
        
        // Destroy Choices.js instance for this row
        const instanceIndex = choicesInstances.findIndex(item => item.element === selectElement);
        if (instanceIndex > -1) {
            choicesInstances[instanceIndex].instance.destroy();
            choicesInstances.splice(instanceIndex, 1);
        }
        
        row.remove();
    } else {
        alert('Minimal harus ada 1 item!');
    }
}
</script>
@endsection
