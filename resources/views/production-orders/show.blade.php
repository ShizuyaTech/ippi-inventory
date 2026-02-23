@extends('layouts.app')

@section('title', 'Detail Production Order')
@section('page-title', 'Detail Production Order')

@section('content')
<div class="mb-6">
    <a href="{{ route('production-orders.index') }}" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke List
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $productionOrder->po_number }}</h2>
                    <p class="text-gray-600">{{ $productionOrder->planned_start_date->format('d M Y') }}</p>
                </div>
                <span class="px-3 py-1 rounded {{ $productionOrder->getStatusBadgeClass() }}">
                    {{ $productionOrder->status }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Production Line</p>
                    <p class="font-semibold">{{ $productionOrder->production_line ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Created By</p>
                    <p class="font-semibold">{{ $productionOrder->user->name }}</p>
                </div>
                @if($productionOrder->actual_start_date)
                <div>
                    <p class="text-sm text-gray-600">Started At</p>
                    <p class="font-semibold">{{ $productionOrder->actual_start_date->format('d M Y H:i') }}</p>
                </div>
                @endif
                @if($productionOrder->actual_complete_date)
                <div>
                    <p class="text-sm text-gray-600">Completed At</p>
                    <p class="font-semibold">{{ $productionOrder->actual_complete_date->format('d M Y H:i') }}</p>
                </div>
                @endif
            </div>

            @if($productionOrder->notes)
            <div class="mt-4 pt-4 border-t">
                <p class="text-sm text-gray-600">Catatan</p>
                <p class="text-gray-800">{{ $productionOrder->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Source Material -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-cubes text-blue-600 mr-2"></i>
                Raw Material (Input)
            </h3>
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-semibold text-lg">{{ $productionOrder->sourceMaterial->material_name }}</div>
                        <div class="text-gray-600">{{ $productionOrder->sourceMaterial->material_code }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">Planned Usage</div>
                        <div class="text-3xl font-bold text-red-600">{{ number_format($productionOrder->source_quantity, 2) }}</div>
                        <div class="text-gray-600">{{ $productionOrder->sourceMaterial->unit_of_measure }}</div>
                    </div>
                </div>

                @if($materialUsage)
                <div class="mt-4 pt-4 border-t border-red-300 grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-xs text-gray-600 mb-1">Planned</div>
                        <div class="text-xl font-bold text-gray-800">{{ number_format($materialUsage['planned'], 2) }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-600 mb-1">Actually Used</div>
                        <div class="text-xl font-bold text-blue-600">{{ number_format($materialUsage['actual_used'], 2) }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-600 mb-1">Returned</div>
                        <div class="text-xl font-bold text-green-600">{{ number_format($materialUsage['returned'], 2) }}</div>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <span class="px-3 py-1 rounded text-sm {{ $materialUsage['yield_rate'] >= 95 ? 'bg-green-100 text-green-800' : ($materialUsage['yield_rate'] >= 80 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        <i class="fas fa-chart-line mr-1"></i>Yield Rate: {{ number_format($materialUsage['yield_rate'], 1) }}%
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Output Materials -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-boxes text-green-600 mr-2"></i>
                Output Materials (Hasil)
            </h3>

            @if($productionOrder->status === 'PROCESSING')
            <form action="{{ route('production-orders.complete', $productionOrder) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    @foreach($productionOrder->outputs as $output)
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-semibold text-lg">{{ $output->outputMaterial->material_name }}</div>
                                <div class="text-gray-600">{{ $output->outputMaterial->material_code }}</div>
                                <div class="mt-2 text-sm text-gray-700">
                                    Expected: <span class="font-semibold">{{ number_format($output->expected_quantity, 2) }} {{ $output->outputMaterial->unit_of_measure }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Actual Output *</label>
                                <input type="hidden" name="outputs[{{ $loop->index }}][id]" value="{{ $output->id }}">
                                <input type="number" name="outputs[{{ $loop->index }}][actual_quantity]" 
                                    value="{{ $output->expected_quantity }}" 
                                    step="0.01" min="0" required
                                    class="w-32 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span class="text-sm text-gray-600 ml-2">{{ $output->outputMaterial->unit_of_measure }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i>Complete Production
                    </button>
                </div>
            </form>
            @else
            <div class="space-y-4">
                @foreach($productionOrder->outputs as $output)
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-semibold text-lg">{{ $output->outputMaterial->material_name }}</div>
                            <div class="text-gray-600">{{ $output->outputMaterial->material_code }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">Expected</div>
                            <div class="text-2xl font-bold text-green-600">{{ number_format($output->expected_quantity, 2) }}</div>
                            @if($output->actual_quantity !== null)
                                <div class="text-sm text-gray-600 mt-1">Actual</div>
                                <div class="text-xl font-bold text-blue-600">{{ number_format($output->actual_quantity, 2) }}</div>
                                <div class="text-xs {{ $output->getYieldRate() >= 95 ? 'text-green-600' : 'text-orange-600' }}">
                                    Yield: {{ number_format($output->getYieldRate(), 1) }}%
                                </div>
                            @endif
                            <div class="text-sm text-gray-600">{{ $output->outputMaterial->unit_of_measure }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <!-- Actions & Summary -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6 sticky top-6">
            <h3 class="text-lg font-semibold mb-4">Actions</h3>

            <div class="space-y-3">
                @if($productionOrder->canStart())
                <form action="{{ route('production-orders.start', $productionOrder) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        onclick="return confirm('Mulai production order ini? Material akan dikurangi dari stock.')">
                        <i class="fas fa-play mr-2"></i>Start Production
                    </button>
                </form>
                <p class="text-xs text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Material akan dikurangi dari stock saat production dimulai
                </p>
                @endif

                @if($productionOrder->canComplete())
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-clock mr-1"></i>
                        Production sedang berjalan. Isi actual output di bawah, lalu klik "Complete Production"
                    </p>
                </div>
                @endif

                @if($productionOrder->canCancel())
                <form action="{{ route('production-orders.cancel', $productionOrder) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                        onclick="return confirm('Batalkan production order ini?{{ $productionOrder->status === 'PROCESSING' ? ' Material akan dikembalikan ke stock.' : '' }}')">
                        <i class="fas fa-times mr-2"></i>Cancel Order
                    </button>
                </form>
                @endif

                @if($productionOrder->isCompleted())
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    <span class="text-green-800 font-semibold">Production Completed</span>
                </div>

                <!-- Yield Summary -->
                <div class="mt-4 pt-4 border-t">
                    <h4 class="font-semibold mb-2">Production Yield</h4>
                    @foreach($productionOrder->outputs as $output)
                    @if($output->actual_quantity)
                    <div class="flex justify-between text-sm py-1">
                        <span class="text-gray-600">{{ $output->outputMaterial->material_name }}</span>
                        <span class="font-semibold {{ $output->getYieldRate() >= 95 ? 'text-green-600' : 'text-orange-600' }}">
                            {{ number_format($output->getYieldRate(), 1) }}%
                        </span>
                    </div>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
