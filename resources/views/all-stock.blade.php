@extends('layouts.app')

@section('title', 'All Data Stock - Material Control System')
@section('page-title', 'All Data Stock')

@section('content')
<!-- Low Stock Statistics by Category -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
    <!-- Raw Material -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs mb-1">Low Stock</p>
                <p class="text-xs font-semibold text-gray-700 mb-2">Raw Material</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($lowStockRawMaterial) }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-industry text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- WIP -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs mb-1">Low Stock</p>
                <p class="text-xs font-semibold text-gray-700 mb-2">WIP</p>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($lowStockWIP) }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-cogs text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Finished Goods -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs mb-1">Low Stock</p>
                <p class="text-xs font-semibold text-gray-700 mb-2">Finished Goods</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($lowStockFinishedGoods) }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Consumables -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs mb-1">Low Stock</p>
                <p class="text-xs font-semibold text-gray-700 mb-2">Consumables</p>
                <p class="text-2xl font-bold text-orange-600">{{ number_format($lowStockConsumables) }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-boxes text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Tools -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs mb-1">Low Stock</p>
                <p class="text-xs font-semibold text-gray-700 mb-2">Tools</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($lowStockTools) }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-tools text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form action="{{ route('all-stock') }}" method="GET">
        <div class="flex flex-wrap gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 min-w-[250px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari kode, nama, kategori, lokasi..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Category Filter -->
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Status Filter -->
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded inline-flex items-center">
                    <i class="fas fa-filter mr-2"></i>Apply
                </button>
                @if(request('search') || request('category') || request('status'))
                    <a href="{{ route('all-stock') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('all-stock', array_merge(request()->all(), ['sort_by' => 'material_code', 'sort_order' => request('sort_by') == 'material_code' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                           class="flex items-center hover:text-blue-600">
                            Material Code
                            @if(request('sort_by') == 'material_code')
                                <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('all-stock', array_merge(request()->all(), ['sort_by' => 'material_name', 'sort_order' => request('sort_by') == 'material_name' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}"
                           class="flex items-center hover:text-blue-600">
                            Material Name
                            @if(request('sort_by') == 'material_name')
                                <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('all-stock', array_merge(request()->all(), ['sort_by' => 'category', 'sort_order' => request('sort_by') == 'category' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}"
                           class="flex items-center hover:text-blue-600">
                            Category
                            @if(request('sort_by') == 'category')
                                <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('all-stock', array_merge(request()->all(), ['sort_by' => 'current_stock', 'sort_order' => request('sort_by') == 'current_stock' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}"
                           class="flex items-center hover:text-blue-600">
                            Current Stock
                            @if(request('sort_by') == 'current_stock')
                                <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('all-stock', array_merge(request()->all(), ['sort_by' => 'minimum_stock', 'sort_order' => request('sort_by') == 'minimum_stock' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}"
                           class="flex items-center hover:text-blue-600">
                            Min Stock
                            @if(request('sort_by') == 'minimum_stock')
                                <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ route('all-stock', array_merge(request()->all(), ['sort_by' => 'unit_of_measure', 'sort_order' => request('sort_by') == 'unit_of_measure' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}"
                           class="flex items-center hover:text-blue-600">
                            Unit
                            @if(request('sort_by') == 'unit_of_measure')
                                <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($materials as $material)
                    <tr class="hover:bg-gray-50 {{ $material->current_stock <= $material->minimum_stock ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $material->material_code }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="font-medium">{{ $material->material_name }}</div>
                            @if($material->description)
                                <div class="text-xs text-gray-500">{{ Str::limit($material->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($material->category)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $material->category == 'Raw Material' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $material->category == 'WIP' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $material->category == 'Finished Goods' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $material->category == 'Consumables' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $material->category == 'Tools' ? 'bg-gray-100 text-gray-800' : '' }}
                                ">
                                    {{ $material->category }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="font-semibold {{ $material->current_stock <= $material->minimum_stock ? 'text-red-600' : 'text-gray-900' }}">
                                {{ number_format($material->current_stock, 2) }}
                            </span>
                            @if($material->current_stock <= $material->minimum_stock)
                                <i class="fas fa-exclamation-triangle text-red-500 ml-1" title="Low Stock"></i>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($material->minimum_stock, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $material->unit_of_measure }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $material->location ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($material->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle"></i> Active
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle"></i> Inactive
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No stock data found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $materials->links() }}
    </div>
</div>

<!-- Info Box -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-600 text-xl"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-800">
                <span class="font-semibold">Total {{ $materials->total() }} items</span> displayed. 
                Items with <span class="text-red-600 font-semibold">red background</span> indicate low stock (current stock ≤ minimum stock).
                Sort columns by clicking on the column headers.
            </p>
        </div>
    </div>
</div>
@endsection
