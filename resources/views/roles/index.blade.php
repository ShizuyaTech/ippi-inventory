@extends('layouts.app')

@section('title', 'Roles & Permissions - Material Control System')
@section('page-title', 'Roles & Permissions Management')

@section('content')
<div class="mb-6 flex justify-end items-center">
    <div class="flex gap-2">
        <a href="{{ route('roles.permissions') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded inline-flex items-center">
            <i class="fas fa-key mr-2"></i>Manage Permissions
        </a>
        <a href="{{ route('roles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>Add New Role
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <!-- Mobile Card View -->
    <div class="block md:hidden p-4">
        <div class="grid grid-cols-2 gap-3">
        @forelse($roles as $role)
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <div class="p-3">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <h3 class="font-semibold text-xs text-gray-900 line-clamp-2">{{ $role->display_name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $role->name }}</p>
                    </div>
                    @if($role->is_active)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 ml-1">Active</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 ml-1">Inactive</span>
                    @endif
                </div>
                <div class="space-y-1 text-xs mb-2">
                    <div>
                        <p class="text-gray-500">Description</p>
                        <p class="text-gray-900 line-clamp-2">{{ $role->description ?: '-' }}</p>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Users:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $role->users_count }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Permissions:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $role->permissions_count }}
                        </span>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-2 border-t border-gray-200">
                    <a href="{{ route('roles.show', $role) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('roles.edit', $role) }}" class="text-yellow-600 hover:text-yellow-800">
                        <i class="fas fa-edit"></i>
                    </a>
                    @if(!in_array($role->name, ['admin', 'staff', 'supplier']))
                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this role?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    @else
                        <span class="text-gray-300">
                            <i class="fas fa-lock"></i>
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 text-center text-gray-500 py-8">
            <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
            <p>No roles found</p>
        </div>
        @endforelse
        </div>
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden md:block">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($roles as $role)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ $role->display_name }}</div>
                            <div class="text-sm text-gray-500">{{ $role->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $role->description ?: '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $role->users_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $role->permissions_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($role->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('roles.show', $role) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('roles.edit', $role) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!in_array($role->name, ['admin', 'staff', 'supplier']))
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-300" title="Default role cannot be deleted">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No roles found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Info Box -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-600 text-xl"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-800">
                <span class="font-semibold">{{ $roles->count() }} roles</span> configured in the system. 
                Default roles (admin, staff, supplier) cannot be deleted. 
                Click <i class="fas fa-edit"></i> to modify role permissions.
            </p>
        </div>
    </div>
</div>
@endsection
