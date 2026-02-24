@extends('layouts.app')

@section('title', 'Stock Opname')
@section('page-title', 'Stock Opname')

@section('content')
<div class="mb-6">
    <a href="{{ route('opname.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded inline-flex items-center">
        <i class="fas fa-plus mr-2"></i>Opname Baru
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <!-- Mobile Card View -->
    <div class="block md:hidden p-4">
        <div class="grid grid-cols-2 gap-3">
        @forelse($opnames as $opname)
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <div class="p-3">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <h3 class="font-semibold text-xs text-gray-900">{{ $opname->opname_number }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $opname->opname_date->format('d/m/Y') }}</p>
                    </div>
                    @if($opname->status == 'DRAFT')
                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800 font-medium ml-1">DRAFT</span>
                    @elseif($opname->status == 'APPROVED')
                        <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800 font-medium ml-1">APPROVED</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-medium ml-1">POSTED</span>
                    @endif
                </div>
                <div class="space-y-1 text-xs mb-2">
                    <div>
                        <p class="text-gray-500">Material</p>
                        <p class="text-gray-900 font-medium line-clamp-2">{{ $opname->material->material_name }}</p>
                    </div>
                    <div class="grid grid-cols-3 gap-1">
                        <div>
                            <p class="text-gray-500">System</p>
                            <p class="text-gray-900 font-medium">{{ number_format($opname->system_stock, 1) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Physical</p>
                            <p class="text-gray-900 font-medium">{{ number_format($opname->physical_stock, 1) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Diff</p>
                            <p class="font-bold {{ $opname->difference != 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($opname->difference, 1) }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-2 border-t border-gray-200">
                    <a href="{{ route('opname.show', $opname) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if($opname->status == 'DRAFT')
                        <form action="{{ route('opname.approve', $opname) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-yellow-600 hover:text-yellow-800">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    @endif
                    @if($opname->status == 'APPROVED')
                        <form action="{{ route('opname.post', $opname) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-800">
                                <i class="fas fa-check-double"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center text-gray-500 py-8">
            <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
            <p>Belum ada data opname</p>
        </div>
        @endforelse
        </div>
        
        @if($opnames->hasPages())
        <div class="pt-4">
            {{ $opnames->links() }}
        </div>
        @endif
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Opname</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Material</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">System Stock</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Physical Stock</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Difference</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($opnames as $opname)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $opname->opname_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $opname->opname_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm">{{ $opname->material->material_name }}</td>
                    <td class="px-6 py-4 text-sm text-right">{{ number_format($opname->system_stock, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-right">{{ number_format($opname->physical_stock, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-right {{ $opname->difference != 0 ? 'font-bold text-red-600' : '' }}">
                        {{ number_format($opname->difference, 2) }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($opname->status == 'DRAFT')
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">DRAFT</span>
                        @elseif($opname->status == 'APPROVED')
                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">APPROVED</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">POSTED</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <a href="{{ route('opname.show', $opname) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if($opname->status == 'DRAFT')
                            <form action="{{ route('opname.approve', $opname) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 mr-2" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        @endif
                        @if($opname->status == 'APPROVED')
                            <form action="{{ route('opname.post', $opname) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900" title="Post">
                                    <i class="fas fa-check-double"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Belum ada data opname</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 bg-gray-50">
            {{ $opnames->links() }}
        </div>
    </div>
</div>
@endsection
