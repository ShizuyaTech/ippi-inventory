@extends('layouts.app')

@section('title', 'Production Order')
@section('page-title', 'Production Order')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex-1 w-full md:w-auto">
            <form action="{{ route('production-orders.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari PO (no. PO, material, output)..." 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('production-orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
        <div>
            <a href="{{ route('production-orders.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Production Order Baru
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PO Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Raw Material</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Output</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Line</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-semibold text-blue-600">{{ $order->po_number }}</div>
                        <div class="text-xs text-gray-500">{{ $order->planned_start_date->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium">{{ $order->sourceMaterial->material_name }}</div>
                        <div class="text-xs text-gray-500">{{ $order->sourceMaterial->material_code }}</div>
                    </td>
                    <td class="px-6 py-4 text-right font-semibold">
                        {{ number_format($order->source_quantity, 2) }} {{ $order->sourceMaterial->unit_of_measure }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            @foreach($order->outputs->take(2) as $output)
                                <div>{{ $output->outputMaterial->material_name }}</div>
                            @endforeach
                            @if($order->outputs->count() > 2)
                                <div class="text-xs text-gray-500">+{{ $order->outputs->count() - 2 }} lainnya</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $order->production_line ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 text-xs rounded {{ $order->getStatusBadgeClass() }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('production-orders.show', $order) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-industry text-4xl text-gray-300 mb-3"></i>
                        <p>Belum ada production order.</p>
                        <a href="{{ route('production-orders.create') }}" class="text-blue-600 hover:text-blue-800">Buat production order pertama →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 bg-gray-50">
        {{ $orders->links() }}
    </div>
</div>
@endsection
