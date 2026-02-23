@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="mb-6">
    <a href="{{ route('transactions.index') }}" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke List
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <div class="border-b pb-4 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">{{ $transaction->transaction_number }}</h3>
                <p class="text-gray-600">{{ $transaction->transaction_date->format('d F Y') }}</p>
            </div>
            <div>
                @if($transaction->transaction_type == 'IN')
                    <span class="px-4 py-2 text-sm rounded bg-green-100 text-green-800 font-semibold">STOCK IN</span>
                @elseif($transaction->transaction_type == 'OUT')
                    <span class="px-4 py-2 text-sm rounded bg-red-100 text-red-800 font-semibold">STOCK OUT</span>
                @else
                    <span class="px-4 py-2 text-sm rounded bg-blue-100 text-blue-800 font-semibold">ADJUSTMENT</span>
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
                    <span class="font-semibold">{{ $transaction->material->material_code }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Material Name</span>
                    <span class="font-semibold">{{ $transaction->material->material_name }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Quantity</span>
                    <span class="font-semibold text-lg text-blue-600">{{ number_format($transaction->quantity, 2) }} {{ $transaction->material->unit_of_measure }}</span>
                </div>
            </div>
        </div>

        <div>
            <h4 class="text-sm font-semibold text-gray-600 mb-4">Informasi Tambahan</h4>
            <div class="space-y-3">
                @if($transaction->warehouse)
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Warehouse</span>
                    <span class="font-semibold">{{ $transaction->warehouse->warehouse_name }}</span>
                </div>
                @endif

                @if($transaction->supplier)
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Supplier</span>
                    <span class="font-semibold">{{ $transaction->supplier->supplier_name }}</span>
                </div>
                @endif

                @if($transaction->customer)
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Customer</span>
                    <span class="font-semibold">{{ $transaction->customer->customer_name }}</span>
                </div>
                @endif

                @if($transaction->reference_number)
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Reference Number</span>
                    <span class="font-semibold">{{ $transaction->reference_number }}</span>
                </div>
                @endif

                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Created By</span>
                    <span class="font-semibold">{{ $transaction->user->name }}</span>
                </div>

                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Created At</span>
                    <span class="font-semibold">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        @if($transaction->notes)
        <div class="md:col-span-2">
            <h4 class="text-sm font-semibold text-gray-600 mb-2">Catatan</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-700">{{ $transaction->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
