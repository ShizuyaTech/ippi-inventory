@extends('layouts.app')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Detail User</h3>
                    <p class="text-sm text-gray-600 mt-1">Informasi lengkap user</p>
                </div>
                <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 h-20 w-20 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-blue-600"></i>
                    </div>
                    <div class="ml-6 flex-1">
                        <h4 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h4>
                        <p class="text-gray-600 mt-1">{{ $user->email }}</p>
                        <div class="mt-2">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                @if($user->role === 'admin') bg-purple-100 text-purple-800
                                @elseif($user->role === 'staff') bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ strtoupper($user->role) }}
                            </span>
                            @if($user->is_active)
                                <span class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Aktif
                                </span>
                            @else
                                <span class="ml-2 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-4">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID User</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->id }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Role</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ strtoupper($user->role) }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($user->is_active)
                                    <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Aktif</span>
                                @else
                                    <span class="text-red-600"><i class="fas fa-times-circle mr-1"></i>Nonaktif</span>
                                @endif
                            </dd>
                        </div>

                        @if($user->supplier)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Supplier Terkait</dt>
                            <dd class="mt-1">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->supplier->supplier_name }}</div>
                                    <div class="text-xs text-gray-500 mt-1">Kode: {{ $user->supplier->supplier_code }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->supplier->contact_person }} - {{ $user->supplier->phone }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->supplier->email }}</div>
                                </div>
                            </dd>
                        </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d M Y H:i') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Terakhir Diupdate</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d M Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                
                @if($user->id !== auth()->id())
                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-2"></i>Hapus User
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
