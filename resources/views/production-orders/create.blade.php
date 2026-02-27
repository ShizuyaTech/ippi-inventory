@extends('layouts.app')

@section('title', 'Production Order Baru')
@section('page-title', 'Buat Production Order Baru')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('production-orders.store') }}" method="POST" id="productionForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Raw Material (Sumber) *</label>
                    <div class="autocomplete-wrapper">
                        <input type="text" 
                            id="sourceMaterialSearch"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            placeholder="Ketik kode atau nama material..."
                            autocomplete="off">
                        <input type="hidden" name="source_material_id" id="sourceMaterial" required>
                    </div>
                    @error('source_material_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Material *</label>
                    <input type="number" name="source_quantity" id="sourceQuantity" value="{{ old('source_quantity') }}" step="0.01" min="0.01" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Berapa <span id="unitText">unit</span> yang akan digunakan?</p>
                    @error('source_quantity')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Production Line</label>
                    <input type="text" name="production_line" value="{{ old('production_line') }}" placeholder="Contoh: Line A"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Planned Start Date *</label>
                    <input type="date" name="planned_start_date" value="{{ old('planned_start_date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-3 flex items-center">
                    <i class="fas fa-boxes text-green-600 mr-2"></i>
                    Expected Output (Hasil yang Diharapkan)
                </h3>
                
                <div id="outputsContainer" class="space-y-3 mb-4">
                    <p class="text-gray-500 text-sm">Pilih raw material dan quantity terlebih dahulu untuk melihat expected output...</p>
                </div>

                <div id="noOutputsWarning" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                        <div>
                            <p class="font-semibold text-yellow-900">Material tidak memiliki output</p>
                            <p class="text-sm text-yellow-800 mt-1">Material ini belum memiliki konfigurasi output. Silakan setup material output terlebih dahulu di halaman material.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('production-orders.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" disabled>
                    <i class="fas fa-save mr-2"></i>Buat Production Order
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Autocomplete dropdown container -->
<div id="autocompleteDropdown" class="autocomplete-dropdown-container" style="display: none;"></div>

<!-- Autocomplete CSS -->
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

<script>
const sourceMaterialsData = @json($sourceMaterials);

document.addEventListener('DOMContentLoaded', function() {
    const sourceMaterialInput = document.getElementById('sourceMaterialSearch');
    const sourceMaterialHidden = document.getElementById('sourceMaterial');
    const sourceQuantityInput = document.getElementById('sourceQuantity');
    const outputsContainer = document.getElementById('outputsContainer');
    const noOutputsWarning = document.getElementById('noOutputsWarning');
    const submitBtn = document.getElementById('submitBtn');
    const unitText = document.getElementById('unitText');
    const dropdown = document.getElementById('autocompleteDropdown');
    let currentMaterialData = null;
    let currentFocus = -1;
    
    // Setup autocomplete
    sourceMaterialInput.addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        currentFocus = -1;
        
        // Clear hidden input and material data when typing
        sourceMaterialHidden.value = '';
        currentMaterialData = null;
        
        if (searchText.length === 0) {
            dropdown.style.display = 'none';
            updateOutputs();
            return;
        }
        
        // Filter materials
        const filtered = sourceMaterialsData.filter(material => {
            const code = material.material_code.toLowerCase();
            const name = material.material_name.toLowerCase();
            return code.includes(searchText) || name.includes(searchText);
        });
        
        displayAutocompleteResults(filtered);
        positionDropdown(sourceMaterialInput, dropdown);
    });
    
    // Show dropdown on focus if there's text
    sourceMaterialInput.addEventListener('focus', function() {
        if (this.value.length > 0 && !sourceMaterialHidden.value) {
            const searchText = this.value.toLowerCase();
            const filtered = sourceMaterialsData.filter(material => {
                const code = material.material_code.toLowerCase();
                const name = material.material_name.toLowerCase();
                return code.includes(searchText) || name.includes(searchText);
            });
            displayAutocompleteResults(filtered);
            positionDropdown(sourceMaterialInput, dropdown);
        }
    });
    
    // Keyboard navigation
    sourceMaterialInput.addEventListener('keydown', function(e) {
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
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!sourceMaterialInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
    
    // Reposition on scroll/resize
    window.addEventListener('scroll', function() {
        if (dropdown.style.display === 'block') {
            positionDropdown(sourceMaterialInput, dropdown);
        }
    }, true);
    
    window.addEventListener('resize', function() {
        if (dropdown.style.display === 'block') {
            positionDropdown(sourceMaterialInput, dropdown);
        }
    });
    
    function positionDropdown(input, dropdown) {
        const rect = input.getBoundingClientRect();
        dropdown.style.top = (rect.bottom + 4) + 'px';
        dropdown.style.left = rect.left + 'px';
        dropdown.style.width = rect.width + 'px';
    }
    
    function displayAutocompleteResults(materials) {
        dropdown.innerHTML = '';
        
        if (materials.length === 0) {
            dropdown.innerHTML = '<div class="autocomplete-no-results">Tidak ada material ditemukan</div>';
            dropdown.style.display = 'block';
            return;
        }
        
        materials.forEach(material => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.textContent = `${material.material_code} - ${material.material_name} (Stock: ${parseFloat(material.current_stock).toFixed(2)} ${material.unit_of_measure})`;
            item.dataset.id = material.id;
            item.dataset.text = `${material.material_code} - ${material.material_name}`;
            item.dataset.stock = material.current_stock;
            item.dataset.unit = material.unit_of_measure;
            
            item.addEventListener('click', function() {
                sourceMaterialInput.value = this.dataset.text;
                sourceMaterialHidden.value = this.dataset.id;
                currentMaterialData = {
                    id: this.dataset.id,
                    stock: this.dataset.stock,
                    unit: this.dataset.unit
                };
                dropdown.style.display = 'none';
                updateOutputs();
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

    function updateOutputs() {
        const materialId = sourceMaterialHidden.value;
        const quantity = parseFloat(sourceQuantityInput.value) || 0;
        const unit = currentMaterialData?.unit || 'unit';
        unitText.textContent = unit;

        if (!materialId || quantity <= 0) {
            outputsContainer.innerHTML = '<p class="text-gray-500 text-sm">Pilih raw material dan quantity terlebih dahulu...</p>';
            noOutputsWarning.classList.add('hidden');
            submitBtn.disabled = true;
            return;
        }

        // Fetch outputs from API
        fetch(`/api/material-outputs?material_id=${materialId}&quantity=${quantity}`)
            .then(response => response.json())
            .then(data => {
                if (data.outputs.length === 0) {
                    outputsContainer.innerHTML = '';
                    noOutputsWarning.classList.remove('hidden');
                    submitBtn.disabled = true;
                    return;
                }

                noOutputsWarning.classList.add('hidden');
                let html = '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">';
                html += '<p class="text-sm text-blue-800"><i class="fas fa-info-circle mr-2"></i>';
                html += `Dari <b>${quantity} ${data.material.unit}</b> ${data.material.name}, akan menghasilkan:</p></div>`;
                
                data.outputs.forEach((output, index) => {
                    html += `
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="font-semibold">${output.output_material_name}</div>
                                    <div class="text-sm text-gray-600">${output.output_material_code}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-green-600">${parseFloat(output.expected_quantity).toFixed(2)}</div>
                                    <div class="text-sm text-gray-600">${output.unit_of_measure}</div>
                                </div>
                                <div class="ml-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="outputs[${index}][selected]" value="1" checked
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 output-checkbox">
                                        <span class="ml-2 text-sm text-gray-700">Produksi</span>
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="outputs[${index}][output_material_id]" value="${output.output_material_id}">
                            <input type="hidden" name="outputs[${index}][expected_quantity]" value="${output.expected_quantity}">
                        </div>
                    `;
                });

                outputsContainer.innerHTML = html;
                
                // Enable submit if at least one output is selected
                updateSubmitButton();
                
                // Add event listeners to checkboxes
                document.querySelectorAll('.output-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', updateSubmitButton);
                });
            })
            .catch(error => {
                console.error('Error fetching outputs:', error);
                outputsContainer.innerHTML = '<p class="text-red-500 text-sm">Error loading outputs</p>';
                submitBtn.disabled = true;
            });
    }

    function updateSubmitButton() {
        const checkedBoxes = document.querySelectorAll('.output-checkbox:checked');
        submitBtn.disabled = checkedBoxes.length === 0;
    }

    sourceQuantityInput.addEventListener('input', updateOutputs);

    // Form submission - remove unchecked outputs
    document.getElementById('productionForm').addEventListener('submit', function(e) {
        document.querySelectorAll('.output-checkbox:not(:checked)').forEach(checkbox => {
            const container = checkbox.closest('.border');
            if (container) {
                container.querySelectorAll('input').forEach(input => input.remove());
            }
        });
    });
});
</script>
@endsection
