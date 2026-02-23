@extends('layouts.app')

@section('title', 'Stock Transaction')
@section('page-title', 'Stock Transaction')

@section('content')
<div class="mb-6">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div class="w-full lg:max-w-md">
            <form action="{{ route('transactions.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari transaksi..." 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="hidden" name="type" value="{{ request('type') }}">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('transactions.index', ['type' => request('type')]) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
        
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('transactions.index', ['type' => 'IN', 'search' => request('search')]) }}" 
               class="px-4 py-2 rounded {{ request('type') == 'IN' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 border' }}">
                Stock IN
            </a>
            <a href="{{ route('transactions.index', ['type' => 'OUT', 'search' => request('search')]) }}" 
               class="px-4 py-2 rounded {{ request('type') == 'OUT' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 border' }}">
                Stock OUT
            </a>
            <a href="{{ route('transactions.index', ['search' => request('search')]) }}" 
               class="px-4 py-2 rounded {{ !request('type') ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">
                Semua
            </a>
            <a href="{{ route('transactions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Transaksi Baru
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
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
    </div>
    
    <div class="px-6 py-4 bg-gray-50">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
