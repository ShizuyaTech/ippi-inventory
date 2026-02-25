@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Daftar User</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola akses user sistem</p>
            </div>
            <div class="hidden md:flex gap-2 flex-wrap">
                <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition">
                    <i class="fas fa-plus mr-2"></i>Tambah User
                </a>
                <a href="{{ route('users.export') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a>
                <a href="{{ route('users.pdf') }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </a>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex gap-1 items-center">
                <form action="{{ route('users.index') }}" method="GET" class="flex gap-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." 
                        class="w-24 md:w-64 px-2 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
                <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm whitespace-nowrap">
                    <span class="md:hidden">Add</span><span class="hidden md:inline"><i class="fas fa-plus mr-2"></i>Tambah User</span>
                </a>
                <a href="{{ route('users.export') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded text-sm">
                    <i class="fas fa-file-excel"></i>
                </a>
                <a href="{{ route('users.pdf') }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm">
                    <i class="fas fa-file-pdf"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="block md:hidden p-4">
        <div class="grid grid-cols-1 gap-4">
        @forelse($users as $user)
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <div class="p-4">
                <div class="flex items-start mb-3">
                    <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="font-semibold text-sm text-gray-900">{{ $user->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $user->email }}</p>
                        <div class="mt-2">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($user->role === 'admin') bg-purple-100 text-purple-800
                                @elseif($user->role === 'staff') bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ strtoupper($user->role) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 text-sm border-t pt-3">
                    @if($user->supplier)
                    <div>
                        <p class="text-xs text-gray-500">Supplier</p>
                        <p class="text-gray-900 font-medium">{{ $user->supplier->supplier_name }}</p>
                        <p class="text-xs text-gray-500">{{ $user->supplier->supplier_code }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        @if($user->is_active)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-3 mt-3 border-t border-gray-200">
                    <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-800">
                        <i class="fas fa-edit"></i>
                    </a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @else
                    <button type="button" class="text-gray-400 cursor-not-allowed" disabled>
                        <i class="fas fa-trash"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-500 py-8">
            <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
            <p>Belum ada data user</p>
        </div>
        @endforelse
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($user->role === 'admin') bg-purple-100 text-purple-800
                            @elseif($user->role === 'staff') bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ strtoupper($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->supplier)
                            <div class="text-sm text-gray-900">{{ $user->supplier->supplier_name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->supplier->supplier_code }}</div>
                        @else
                            <span class="text-sm text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->is_active)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @else
                            <button type="button" class="text-gray-400 cursor-not-allowed" title="Tidak dapat menghapus diri sendiri" disabled>
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Belum ada data user</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->onEachSide(2)->links() }}
    </div>
    @endif
</div>
@endsection
