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
    <div class="overflow-x-auto">
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
    </div>
    
    <div class="px-6 py-4 bg-gray-50">
        {{ $opnames->links() }}
    </div>
</div>
@endsection
