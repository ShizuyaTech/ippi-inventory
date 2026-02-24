@extends('layouts.app')

@section('title', 'Warehouse - Material Control System')
@section('page-title', 'Warehouse Master')

@section('content')
<div class="mb-6">
    <div class="flex gap-2 items-center">
        <!-- Search Form -->
        <form action="{{ route('warehouses.index') }}" method="GET" class="flex gap-1 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." 
                class="flex-1 min-w-0 px-2 lg:px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 lg:px-4 py-2 rounded flex-shrink-0" title="Cari">
                <i class="fas fa-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('warehouses.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 lg:px-4 py-2 rounded flex-shrink-0" title="Reset">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
        
        <!-- Action Buttons -->
        <div class="flex gap-1 lg:gap-2 flex-shrink-0">
            <a href="{{ route('warehouses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 lg:px-4 py-2 rounded inline-flex items-center" title="Tambah Warehouse">
                <i class="fas fa-plus"></i>
                <span class="hidden lg:inline lg:ml-2">Tambah Warehouse</span>
            </a>
            <button onclick="document.getElementById('importModal').classList.remove('hidden')" 
                class="bg-green-600 hover:bg-green-700 text-white px-3 lg:px-4 py-2 rounded inline-flex items-center" title="Import Excel">
                <i class="fas fa-file-import"></i>
                <span class="hidden lg:inline lg:ml-2">Import Excel</span>
            </button>
            <a href="{{ route('warehouses.export') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 lg:px-4 py-2 rounded inline-flex items-center" title="Export Excel">
                <i class="fas fa-file-excel"></i>
                <span class="hidden lg:inline lg:ml-2">Export Excel</span>
            </a>
            <a href="{{ route('warehouses.pdf') }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-3 lg:px-4 py-2 rounded inline-flex items-center" title="Export PDF">
                <i class="fas fa-file-pdf"></i>
                <span class="hidden lg:inline lg:ml-2">Export PDF</span>
            </a>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Import Warehouses dari Excel</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Download Template Section -->
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
            <p class="text-sm text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-1"></i>Belum punya template?
            </p>
            <a href="{{ route('warehouses.template') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-semibold">
                <i class="fas fa-download mr-2"></i>Download Template Excel
            </a>
        </div>
        
        <form action="{{ route('warehouses.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Pilih File Excel</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Format: .xlsx, .xls, .csv</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                    Batal
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-upload mr-2"></i>Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <!-- Mobile Card View -->
    <div class="block md:hidden p-4">
        <div class="grid grid-cols-2 gap-3">
        @forelse($warehouses as $warehouse)
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <div class="p-3">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <h3 class="font-semibold text-xs text-gray-900 line-clamp-2">{{ $warehouse->warehouse_name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $warehouse->warehouse_code }}</p>
                    </div>
                    @if($warehouse->is_active)
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-medium ml-1">Aktif</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800 font-medium ml-1">Nonaktif</span>
                    @endif
                </div>
                <div class="space-y-1 text-xs mb-2">
                    <div>
                        <p class="text-gray-500">Description</p>
                        <p class="text-gray-900 line-clamp-2">{{ $warehouse->description ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Location</p>
                        <p class="text-gray-900 font-medium">{{ $warehouse->location ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-2 border-t border-gray-200">
                    <a href="{{ route('warehouses.edit', $warehouse) }}" class="text-yellow-600 hover:text-yellow-800">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center text-gray-500 py-8">
            <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
            <p>Belum ada data warehouse</p>
        </div>
        @endforelse
        </div>
        
        @if($warehouses->hasPages())
        <div class="pt-4">
            {{ $warehouses->links() }}
        </div>
        @endif
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Warehouse</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($warehouses as $warehouse)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $warehouse->warehouse_code }}</td>
                    <td class="px-6 py-4 text-sm">{{ $warehouse->warehouse_name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $warehouse->description ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $warehouse->location ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($warehouse->is_active)
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap text-sm">
                        <a href="{{ route('warehouses.edit', $warehouse) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data warehouse</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 bg-gray-50">
            {{ $warehouses->links() }}
        </div>
    </div>
</div>
@endsection
