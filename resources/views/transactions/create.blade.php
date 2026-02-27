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

                <div class="overflow-visible">
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
                                    <div class="autocomplete-wrapper">
                                        <input type="text" 
                                            class="material-search w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                            placeholder="Ketik kode atau nama material..."
                                            autocomplete="off"
                                            data-index="0">
                                        <input type="hidden" name="items[0][material_id]" class="material-id" required>
                                    </div>
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

<!-- Autocomplete dropdown container (outside table, for proper positioning) -->
<div id="autocompleteDropdown" class="autocomplete-dropdown-container" style="display: none;"></div>

<!-- Choices.js CSS -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css"> -->

<!-- Custom CSS for autocomplete -->
<style>
    .autocomplete-wrapper {
        position: relative;
    }
    
    .autocomplete-dropdown-container {
        position: fixed;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        max-height: 400px;
        overflow-y: auto;
        z-index: 9999;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        min-width: 300px;
    }
    
    .autocomplete-item {
        padding: 10px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
        line-height: 1.5;
    }
    
    .autocomplete-item:last-child {
        border-bottom: none;
    }
    
    .autocomplete-item:hover,
    .autocomplete-item.active {
        background-color: #3b82f6;
        color: white;
    }
    
    .autocomplete-no-results {
        padding: 10px 12px;
        color: #6b7280;
        font-size: 14px;
        text-align: center;
    }
</style>

<!-- Choices.js JavaScript -->
<!-- <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script> -->

<script>
let itemCounter = 1;
const materialsData = @json($materials);
let currentActiveInput = null;

// Initialize autocomplete for all material search inputs
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing autocomplete...');
    console.log('Materials count:', materialsData.length);
    initializeAllAutocomplete();
});

function initializeAllAutocomplete() {
    const searchInputs = document.querySelectorAll('.material-search');
    
    searchInputs.forEach((input) => {
        if (!input.dataset.autocompleteInitialized) {
            setupAutocomplete(input);
            input.dataset.autocompleteInitialized = 'true';
        }
    });
}

function setupAutocomplete(input) {
    const wrapper = input.closest('.autocomplete-wrapper');
    const dropdown = document.getElementById('autocompleteDropdown');
    const hiddenInput = wrapper.querySelector('.material-id');
    let currentFocus = -1;
    
    // Filter on input
    input.addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        currentFocus = -1;
        currentActiveInput = this;
        
        // Clear hidden input when typing
        hiddenInput.value = '';
        
        if (searchText.length === 0) {
            dropdown.style.display = 'none';
            return;
        }
        
        // Filter materials
        const filtered = materialsData.filter(material => {
            const code = material.material_code.toLowerCase();
            const name = material.material_name.toLowerCase();
            return code.includes(searchText) || name.includes(searchText);
        });
        
        displayResults(filtered, dropdown, input, hiddenInput);
        positionDropdown(input, dropdown);
    });
    
    // Show dropdown on focus if there's text
    input.addEventListener('focus', function() {
        currentActiveInput = this;
        if (this.value.length > 0) {
            const searchText = this.value.toLowerCase();
            const filtered = materialsData.filter(material => {
                const code = material.material_code.toLowerCase();
                const name = material.material_name.toLowerCase();
                return code.includes(searchText) || name.includes(searchText);
            });
            displayResults(filtered, dropdown, input, hiddenInput);
            positionDropdown(input, dropdown);
        }
    });
    
    // Keyboard navigation
    input.addEventListener('keydown', function(e) {
        const items = dropdown.querySelectorAll('.autocomplete-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentFocus++;
            if (currentFocus >= items.length) currentFocus = 0;
            setActive(items, currentFocus);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentFocus--;
            if (currentFocus < 0) currentFocus = items.length - 1;
            setActive(items, currentFocus);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (currentFocus > -1 && items[currentFocus]) {
                items[currentFocus].click();
            }
        } else if (e.key === 'Escape') {
            dropdown.style.display = 'none';
        }
    });
}

function positionDropdown(input, dropdown) {
    const rect = input.getBoundingClientRect();
    dropdown.style.top = (rect.bottom + 4) + 'px';
    dropdown.style.left = rect.left + 'px';
    dropdown.style.width = rect.width + 'px';
}

function displayResults(materials, dropdown, input, hiddenInput) {
    dropdown.innerHTML = '';
    
    if (materials.length === 0) {
        dropdown.innerHTML = '<div class="autocomplete-no-results">Tidak ada material ditemukan</div>';
        dropdown.style.display = 'block';
        return;
    }
    
    materials.forEach(material => {
        const item = document.createElement('div');
        item.className = 'autocomplete-item';
        item.textContent = `${material.material_code} - ${material.material_name} (${parseFloat(material.current_stock).toFixed(2)} ${material.unit_of_measure})`;
        item.dataset.id = material.id;
        item.dataset.text = `${material.material_code} - ${material.material_name}`;
        
        item.addEventListener('click', function() {
            input.value = this.dataset.text;
            hiddenInput.value = this.dataset.id;
            dropdown.style.display = 'none';
        });
        
        dropdown.appendChild(item);
    });
    
    dropdown.style.display = 'block';
}

function setActive(items, index) {
    items.forEach(item => item.classList.remove('active'));
    if (items[index]) {
        items[index].classList.add('active');
        items[index].scrollIntoView({ block: 'nearest' });
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('autocompleteDropdown');
    if (currentActiveInput && !currentActiveInput.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});

// Reposition dropdown on scroll
window.addEventListener('scroll', function() {
    const dropdown = document.getElementById('autocompleteDropdown');
    if (dropdown.style.display === 'block' && currentActiveInput) {
        positionDropdown(currentActiveInput, dropdown);
    }
}, true);

// Reposition dropdown on window resize
window.addEventListener('resize', function() {
    const dropdown = document.getElementById('autocompleteDropdown');
    if (dropdown.style.display === 'block' && currentActiveInput) {
        positionDropdown(currentActiveInput, dropdown);
    }
});

function addItemRow() {
    const tbody = document.getElementById('itemsTableBody');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row';
    
    newRow.innerHTML = `
        <td class="border px-4 py-2">
            <div class="autocomplete-wrapper">
                <input type="text" 
                    class="material-search w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Ketik kode atau nama material..."
                    autocomplete="off"
                    data-index="${itemCounter}">
                <input type="hidden" name="items[${itemCounter}][material_id]" class="material-id" required>
            </div>
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
    
    // Initialize autocomplete for the new row
    initializeAllAutocomplete();
}

function removeItemRow(button) {
    const tbody = document.getElementById('itemsTableBody');
    if (tbody.children.length > 1) {
        button.closest('tr').remove();
    } else {
        alert('Minimal harus ada 1 item!');
    }
}
</script>
@endsection
