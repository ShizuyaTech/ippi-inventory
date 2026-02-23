@extends('layouts.app')

@section('title', 'Detail Stock Opname')
@section('page-title', 'Detail Stock Opname')

@section('content')
<div class="mb-6">
    <a href="{{ route('opname.index') }}" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke List
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <div class="border-b pb-4 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">{{ $opname->opname_number }}</h3>
                <p class="text-gray-600">{{ $opname->opname_date->format('d F Y') }}</p>
            </div>
            <div class="space-x-2">
                @if($opname->status == 'DRAFT')
                    <span class="px-4 py-2 text-sm rounded bg-gray-100 text-gray-800 font-semibold">DRAFT</span>
                    <form action="{{ route('opname.approve', $opname) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                            <i class="fas fa-check mr-2"></i>Approve
                        </button>
                    </form>
                @elseif($opname->status == 'APPROVED')
                    <span class="px-4 py-2 text-sm rounded bg-yellow-100 text-yellow-800 font-semibold">APPROVED</span>
                    <form action="{{ route('opname.post', $opname) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            <i class="fas fa-check-double mr-2"></i>Post
                        </button>
                    </form>
                @else
                    <span class="px-4 py-2 text-sm rounded bg-green-100 text-green-800 font-semibold">POSTED</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h4 class="text-sm font-semibold text-gray-600 mb-4">Informasi Material</h4>
            <div class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Material Code</span>
                    <span class="font-semibold">{{ $opname->material->material_code }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Material Name</span>
                    <span class="font-semibold">{{ $opname->material->material_name }}</span>
                </div>
                @if($opname->warehouse)
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Warehouse</span>
                    <span class="font-semibold">{{ $opname->warehouse->warehouse_name }}</span>
                </div>
                @endif
            </div>
        </div>

        <div>
            <h4 class="text-sm font-semibold text-gray-600 mb-4">Stock Comparison</h4>
            <div class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">System Stock</span>
                    <span class="font-semibold">{{ number_format($opname->system_stock, 2) }} {{ $opname->material->unit_of_measure }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Physical Stock</span>
                    <span class="font-semibold">{{ number_format($opname->physical_stock, 2) }} {{ $opname->material->unit_of_measure }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Difference</span>
                    <span class="font-semibold text-lg {{ $opname->difference != 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $opname->difference > 0 ? '+' : '' }}{{ number_format($opname->difference, 2) }} {{ $opname->material->unit_of_measure }}
                    </span>
                </div>
            </div>
        </div>

        @if($opname->difference != 0)
        <div class="md:col-span-2 bg-{{ abs($opname->difference) > 0 ? 'yellow' : 'green' }}-50 p-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-{{ abs($opname->difference) > 0 ? 'yellow' : 'green' }}-600 text-xl mr-3"></i>
                <div>
                    <p class="font-semibold text-gray-800">Stock Difference Detected</p>
                    <p class="text-sm text-gray-600">
                        @if($opname->difference > 0)
                            Physical stock lebih tinggi {{ number_format($opname->difference, 2) }} {{ $opname->material->unit_of_measure }} dari system stock
                        @else
                            Physical stock lebih rendah {{ number_format(abs($opname->difference), 2) }} {{ $opname->material->unit_of_measure }} dari system stock
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if($opname->notes)
        <div class="md:col-span-2">
            <h4 class="text-sm font-semibold text-gray-600 mb-2">Catatan</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-700">{{ $opname->notes }}</p>
            </div>
        </div>
        @endif

        <div class="md:col-span-2 border-t pt-4">
            <h4 class="text-sm font-semibold text-gray-600 mb-3">Audit Information</h4>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Created By</p>
                    <p class="font-semibold">{{ $opname->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Created At</p>
                    <p class="font-semibold">{{ $opname->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($opname->updated_at != $opname->created_at)
                <div>
                    <p class="text-sm text-gray-600">Last Updated</p>
                    <p class="font-semibold">{{ $opname->updated_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
