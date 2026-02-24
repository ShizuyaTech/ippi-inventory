@extends('layouts.app')

@section('title', 'Dashboard - Material Control System')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Material IN -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-lg p-3">
                <i class="fas fa-arrow-down text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Material IN (Today)</p>
                <p class="text-2xl font-semibold text-gray-800">{{ number_format($totalMaterialIn, 0) }}</p>
            </div>
        </div>
    </div>

    <!-- Total Material OUT -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-red-500 rounded-lg p-3">
                <i class="fas fa-arrow-up text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Material OUT (Today)</p>
                <p class="text-2xl font-semibold text-gray-800">{{ number_format($totalMaterialOut, 0) }}</p>
            </div>
        </div>
    </div>

    <!-- Total Production Orders -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-500 rounded-lg p-3">
                <i class="fas fa-industry text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Production Orders</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $totalProductionOrders }}</p>
            </div>
        </div>
    </div>

    <!-- Low Stock -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-orange-500 rounded-lg p-3">
                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Stock Minimum</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $lowStockMaterials }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Today's Activity -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">
            <i class="fas fa-calendar-day mr-2 text-blue-600"></i>Aktivitas Hari Ini
        </h3>
        
        <!-- Stock IN by Supplier -->
        <div class="mb-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-arrow-down text-green-600 mr-1"></i>Stock In per Supplier
            </h4>
            <div class="space-y-2">
                @forelse($stockInBySupplier as $item)
                <div class="flex justify-between items-center bg-green-50 p-3 rounded-lg">
                    <span class="text-sm text-gray-700">{{ $item->supplier->supplier_name ?? 'N/A' }}</span>
                    <span class="text-sm font-bold text-green-600">{{ number_format($item->total_qty, 2) }}</span>
                </div>
                @empty
                <div class="bg-gray-50 p-3 rounded-lg text-center text-sm text-gray-500">
                    Tidak ada stock in hari ini
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Stock OUT by Customer -->
        <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-arrow-up text-red-600 mr-1"></i>Stock Out per Customer
            </h4>
            <div class="space-y-2">
                @forelse($stockOutByCustomer as $item)
                <div class="flex justify-between items-center bg-red-50 p-3 rounded-lg">
                    <span class="text-sm text-gray-700">{{ $item->customer->customer_name ?? 'N/A' }}</span>
                    <span class="text-sm font-bold text-red-600">{{ number_format($item->total_qty, 2) }}</span>
                </div>
                @empty
                <div class="bg-gray-50 p-3 rounded-lg text-center text-sm text-gray-500">
                    Tidak ada stock out hari ini
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">
            <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>Material Stock Minimum
        </h3>
        
        <!-- Mobile Card View -->
        <div class="block md:hidden space-y-3">
            @forelse($lowStockItems as $item)
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                <p class="font-medium text-sm text-gray-900 mb-2">{{ $item->material_name }}</p>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="text-xs text-gray-500">Current Stock</p>
                        <p class="text-sm font-bold text-red-600">{{ number_format($item->current_stock, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Min Stock</p>
                        <p class="text-sm font-medium text-gray-600">{{ number_format($item->minimum_stock, 2) }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 text-sm py-4">
                <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                <p>Tidak ada material dengan stock minimum</p>
            </div>
            @endforelse
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 text-sm text-gray-600">Material</th>
                        <th class="text-right py-2 text-sm text-gray-600">Stock</th>
                        <th class="text-right py-2 text-sm text-gray-600">Min</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockItems as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 text-sm">{{ $item->material_name }}</td>
                        <td class="text-right text-sm text-red-600 font-semibold">{{ number_format($item->current_stock, 2) }}</td>
                        <td class="text-right text-sm text-gray-600">{{ number_format($item->minimum_stock, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-gray-500 text-sm">Tidak ada material dengan stock minimum</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">
        <i class="fas fa-history mr-2 text-blue-600"></i>Transaksi Terakhir
    </h3>
    
    <!-- Mobile Card View -->
    <div class="block md:hidden space-y-3">
        @forelse($recentTransactions as $transaction)
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <p class="font-medium text-sm text-gray-900">{{ $transaction->transaction_number }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $transaction->transaction_date->format('d/m/Y') }}</p>
                </div>
                @if($transaction->transaction_type == 'IN')
                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-medium">IN</span>
                @elseif($transaction->transaction_type == 'OUT')
                    <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800 font-medium">OUT</span>
                @else
                    <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 font-medium">ADJ</span>
                @endif
            </div>
            <div class="space-y-2">
                <div>
                    <p class="text-xs text-gray-500">Material</p>
                    <p class="text-sm text-gray-900">{{ $transaction->material->material_name }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-gray-500">Quantity</p>
                        <p class="text-sm font-semibold text-gray-900">{{ number_format($transaction->quantity, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">User</p>
                        <p class="text-sm text-gray-900">{{ $transaction->user->name }}</p>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-500 text-sm py-8">
            <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
            <p>Belum ada transaksi</p>
        </div>
        @endforelse
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2 px-2 text-sm text-gray-600">No. Transaksi</th>
                    <th class="text-left py-2 px-2 text-sm text-gray-600">Tanggal</th>
                    <th class="text-left py-2 px-2 text-sm text-gray-600">Tipe</th>
                    <th class="text-left py-2 px-2 text-sm text-gray-600">Material</th>
                    <th class="text-right py-2 px-2 text-sm text-gray-600">Qty</th>
                    <th class="text-left py-2 px-2 text-sm text-gray-600">User</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $transaction)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-2 text-sm">{{ $transaction->transaction_number }}</td>
                    <td class="py-2 px-2 text-sm">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                    <td class="py-2 px-2">
                        @if($transaction->transaction_type == 'IN')
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">IN</span>
                        @elseif($transaction->transaction_type == 'OUT')
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">OUT</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">ADJ</span>
                        @endif
                    </td>
                    <td class="py-2 px-2 text-sm">{{ $transaction->material->material_name }}</td>
                    <td class="text-right py-2 px-2 text-sm font-semibold">{{ number_format($transaction->quantity, 2) }}</td>
                    <td class="py-2 px-2 text-sm">{{ $transaction->user->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-4 text-center text-gray-500 text-sm">Belum ada transaksi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
