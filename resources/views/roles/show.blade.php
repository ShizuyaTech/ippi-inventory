@extends('layouts.app')

@section('title', 'View Role - Material Control System')
@section('page-title', 'Role Details: ' . $role->display_name)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('roles.index') }}" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Back to Roles
    </a>
    <a href="{{ route('roles.edit', $role) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded inline-flex items-center">
        <i class="fas fa-edit mr-2"></i>Edit Role
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Role Information</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Role Name</label>
            <p class="text-gray-900 font-semibold">{{ $role->name }}</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Display Name</label>
            <p class="text-gray-900 font-semibold">{{ $role->display_name }}</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
            @if($role->is_active)
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                    <i class="fas fa-check-circle mr-1"></i>Active
                </span>
            @else
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                    <i class="fas fa-times-circle mr-1"></i>Inactive
                </span>
            @endif
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Total Users</label>
            <p class="text-gray-900 font-semibold">{{ $role->users()->count() }} users</p>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
            <p class="text-gray-700">{{ $role->description ?: 'No description provided' }}</p>
        </div>
    </div>
</div>

<!-- Permissions -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">
        Assigned Permissions ({{ $role->permissions->count() }})
    </h3>

    @if($permissionsByGroup->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($permissionsByGroup as $group => $groupPermissions)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 mb-3 capitalize flex items-center">
                        <i class="fas fa-folder text-blue-600 mr-2"></i>
                        {{ ucfirst($group) }}
                    </h4>
                    <ul class="space-y-2">
                        @foreach($groupPermissions as $permission)
                            <li class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-check text-green-600 mr-2"></i>
                                {{ $permission->display_name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center text-gray-500 py-8">
            <i class="fas fa-exclamation-circle text-4xl mb-2"></i>
            <p>No permissions assigned to this role</p>
        </div>
    @endif
</div>
@endsection
