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
                    <select name="source_material_id" id="sourceMaterial" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Raw Material</option>
                        @foreach($sourceMaterials as $material)
                            <option value="{{ $material->id }}" data-stock="{{ $material->current_stock }}" data-unit="{{ $material->unit_of_measure }}">
                                {{ $material->material_code }} - {{ $material->material_name }} (Stock: {{ number_format($material->current_stock, 2) }} {{ $material->unit_of_measure }})
                            </option>
                        @endforeach
                    </select>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sourceMaterialSelect = document.getElementById('sourceMaterial');
    const sourceQuantityInput = document.getElementById('sourceQuantity');
    const outputsContainer = document.getElementById('outputsContainer');
    const noOutputsWarning = document.getElementById('noOutputsWarning');
    const submitBtn = document.getElementById('submitBtn');
    const unitText = document.getElementById('unitText');

    function updateOutputs() {
        const materialId = sourceMaterialSelect.value;
        const quantity = parseFloat(sourceQuantityInput.value) || 0;
        const unit = sourceMaterialSelect.selectedOptions[0]?.dataset.unit || 'unit';
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

    sourceMaterialSelect.addEventListener('change', updateOutputs);
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
