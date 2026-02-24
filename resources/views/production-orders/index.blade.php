@extends('layouts.app')

@section('title', 'Production Order')
@section('page-title', 'Production Order')

@section('content')
<div class="mb-6">
    <div class="flex gap-2 items-center">
        <form action="{{ route('production-orders.index') }}" method="GET" class="flex gap-2 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari PO..." 
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                <i class="fas fa-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('production-orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
        <div>
            <a href="{{ route('production-orders.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm whitespace-nowrap inline-flex items-center">
                <i class="fas fa-plus mr-1"></i>Prod. Order
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <!-- Mobile Card View -->
    <div class="block md:hidden p-4">
        <div class="grid grid-cols-2 gap-3">
        @forelse($orders as $order)
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <div class="p-3">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <h3 class="font-semibold text-xs text-blue-600">{{ $order->po_number }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $order->planned_start_date->format('d/m/Y') }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded {{ $order->getStatusBadgeClass() }} ml-1">
                        {{ $order->status }}
                    </span>
                </div>
                <div class="space-y-1 text-xs mb-2">
                    <div>
                        <p class="text-gray-500">Raw Material</p>
                        <p class="text-gray-900 font-medium line-clamp-2">{{ $order->sourceMaterial->material_name }}</p>
                        <p class="text-xs text-gray-500">{{ $order->sourceMaterial->material_code }}</p>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Qty:</span>
                        <span class="text-gray-900 font-semibold">{{ number_format($order->source_quantity, 2) }} {{ $order->sourceMaterial->unit_of_measure }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Line:</span>
                        <span class="text-gray-900">{{ $order->production_line ?? '-' }}</span>
                    </div>
                    <div>
                        <p class="text-gray-500">Output</p>
                        @foreach($order->outputs->take(2) as $output)
                            <p class="text-xs text-gray-900 line-clamp-1">{{ $output->outputMaterial->material_name }}</p>
                        @endforeach
                        @if($order->outputs->count() > 2)
                            <p class="text-xs text-gray-500">+{{ $order->outputs->count() - 2 }} more</p>
                        @endif
                    </div>
                </div>
                <div class="flex justify-end pt-2 border-t border-gray-200">
                    <a href="{{ route('production-orders.show', $order) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-eye mr-1"></i> Detail
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center text-gray-500 py-8">
            <i class="fas fa-industry text-gray-300 text-4xl mb-3"></i>
            <p>Belum ada production order.</p>
            <a href="{{ route('production-orders.create') }}" class="text-blue-600 hover:text-blue-800 block mt-2">Buat production order pertama →</a>
        </div>
        @endforelse
        </div>
        
        @if($orders->hasPages())
        <div class="pt-4">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
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
        
        <div class="px-6 py-4 bg-gray-50">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
