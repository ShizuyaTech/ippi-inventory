@extends('layouts.app')

@section('title', 'Tambah Material')
@section('page-title', 'Tambah Material Baru')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('materials.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Material *</label>
                    <input type="text" name="material_code" value="{{ old('material_code') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('material_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Material *</label>
                    <input type="text" name="material_name" value="{{ old('material_name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('material_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit of Measure *</label>
                    <select name="unit_of_measure" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih UOM</option>
                        <option value="PCS">PCS (Pieces)</option>
                        <option value="KG">KG (Kilogram)</option>
                        <option value="TON">TON</option>
                        <option value="M">M (Meter)</option>
                        <option value="M2">M2 (Square Meter)</option>
                        <option value="ROLL">ROLL</option>
                        <option value="BOX">BOX</option>
                        <option value="SHEET">SHEET (Lembaran)</option>
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
                        <option value="Raw Material">Raw Material</option>
                        <option value="WIP">WIP (Work In Process)</option>
                        <option value="Finished Goods">Finished Goods</option>
                        <option value="Consumables">Consumables</option>
                        <option value="Tools">Tools</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock *</label>
                    <input type="number" name="minimum_stock" value="{{ old('minimum_stock', 0) }}" step="0.01" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('minimum_stock')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('materials.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>

        <!-- Info about Material Output -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-1">Material Output</h4>
                    <p class="text-sm text-blue-800">Setelah material ini tersimpan, Anda dapat menambahkan <b>Material Output</b> untuk menentukan material apa saja yang dihasilkan dari material ini.</p>
                    <p class="text-xs text-blue-700 mt-2"><i class="fas fa-lightbulb mr-1"></i>Contoh: 1 SHEET Steel → dapat menghasilkan 2 PCS Bracket + 12 PCS Connector Plate</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
