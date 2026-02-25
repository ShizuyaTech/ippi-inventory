@extends('layouts.app')

@section('title', 'Stock Transaction')
@section('page-title', 'Stock Transaction')

@section('content')
<div class="mb-6 px-4 md:px-6">
    <div class="flex gap-2 items-center">
        <!-- Search Form -->
        <form action="{{ route('transactions.index') }}" method="GET" class="flex gap-1 flex-1 max-w-[35%] sm:max-w-xs lg:max-w-md">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." 
                class="flex-1 min-w-0 px-2 lg:px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <input type="hidden" name="type" value="{{ request('type') }}">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 lg:px-4 py-2 rounded flex-shrink-0" title="Cari">
                <i class="fas fa-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('transactions.index', ['type' => request('type')]) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 lg:px-4 py-2 rounded flex-shrink-0" title="Reset">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
        
        <!-- Action Buttons -->
        <div class="flex gap-1 sm:gap-1.5 lg:gap-2 flex-shrink-0">
            <a href="{{ route('transactions.index', ['search' => request('search')]) }}" 
               class="px-3 lg:px-4 py-2 rounded text-sm flex items-center {{ !request('type') ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}" title="Semua Transaksi">
                <i class="fas fa-list"></i>
                <span class="hidden lg:inline lg:ml-2">Semua</span>
            </a>
            <a href="{{ route('transactions.index', ['type' => 'IN', 'search' => request('search')]) }}" 
               class="px-3 lg:px-4 py-2 rounded text-sm flex items-center {{ request('type') == 'IN' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 border' }}" title="Material IN">
                <i class="fas fa-arrow-down"></i>
                <span class="hidden lg:inline lg:ml-2">IN</span>
            </a>
            <a href="{{ route('transactions.index', ['type' => 'OUT', 'search' => request('search')]) }}" 
               class="px-3 lg:px-4 py-2 rounded text-sm flex items-center {{ request('type') == 'OUT' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 border' }}" title="Material OUT">
                <i class="fas fa-arrow-up"></i>
                <span class="hidden lg:inline lg:ml-2">OUT</span>
            </a>
            <a href="{{ route('transactions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 lg:px-4 py-2 rounded inline-flex items-center" title="Tambah Transaksi">
                <i class="fas fa-plus"></i>
                <span class="hidden lg:inline lg:ml-2">Transaksi</span>
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <!-- Mobile Card View -->
    <div class="block md:hidden p-4">
        <div class="grid grid-cols-2 gap-3">
        @forelse($transactions as $transaction)
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <div class="p-3">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <h3 class="font-semibold text-xs text-gray-900">{{ $transaction->transaction_number }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $transaction->transaction_date->format('d/m/Y') }}</p>
                    </div>
                    @if($transaction->transaction_type == 'IN')
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-semibold ml-1">IN</span>
                    @elseif($transaction->transaction_type == 'OUT')
                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800 font-semibold ml-1">OUT</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 font-semibold ml-1">ADJ</span>
                    @endif
                </div>
                <div class="space-y-1 text-xs mb-2">
                    <div>
                        <p class="text-gray-500">Material</p>
                        <p class="text-gray-900 font-medium line-clamp-2">{{ $transaction->material->material_name }}</p>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Qty:</span>
                        <span class="text-gray-900 font-semibold">{{ number_format($transaction->quantity, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ref:</span>
                        <span class="text-gray-900 truncate ml-1">{{ $transaction->reference_number ?? '-' }}</span>
                    </div>
                </div>
                <div class="flex justify-end pt-2 border-t border-gray-200">
                    <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-eye mr-1"></i> Detail
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center text-gray-500 py-8">
            <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
            <p>Belum ada transaksi</p>
        </div>
        @endforelse
        </div>
        
        @if($transactions->hasPages())
        <div class="pt-4">
            {{ $transactions->onEachSide(2)->links() }}
        </div>
        @endif
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Transaksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Material</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referensi</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $transaction->transaction_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($transaction->transaction_type == 'IN')
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-semibold">IN</span>
                        @elseif($transaction->transaction_type == 'OUT')
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800 font-semibold">OUT</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 font-semibold">ADJ</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $transaction->material->material_name }}</td>
                    <td class="px-6 py-4 text-sm text-right font-semibold">{{ number_format($transaction->quantity, 2) }}</td>
                    <td class="px-6 py-4 text-sm">{{ $transaction->reference_number ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada transaksi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 bg-gray-50">
            {{ $transactions->onEachSide(2)->links() }}
        </div>
    </div>
</div>
@endsection
