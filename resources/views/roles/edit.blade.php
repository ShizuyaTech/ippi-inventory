@extends('layouts.app')

@section('title', 'Edit Role - Material Control System')
@section('page-title', 'Edit Role: ' . $role->display_name)

@section('content')
<div class="mb-6">
    <a href="{{ route('roles.index') }}" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Back to Roles
    </a>
</div>

<form action="{{ route('roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Role Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role Name (slug) *</label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                    placeholder="e.g., warehouse-manager">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Lowercase, no spaces (use hyphens or underscores)</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Display Name *</label>
                <input type="text" name="display_name" value="{{ old('display_name', $role->display_name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('display_name') border-red-500 @enderror"
                    placeholder="e.g., Warehouse Manager">
                @error('display_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Brief description of this role...">{{ old('description', $role->description) }}</textarea>
        </div>
    </div>

    <!-- Permissions -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Permissions</h3>
            <div class="flex gap-2">
                <button type="button" onclick="checkAll()" class="text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-check-square mr-1"></i>Check All
                </button>
                <button type="button" onclick="uncheckAll()" class="text-sm text-red-600 hover:text-red-800">
                    <i class="fas fa-square mr-1"></i>Uncheck All
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($permissions as $group => $groupPermissions)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 mb-3 capitalize flex items-center">
                        <i class="fas fa-folder text-blue-600 mr-2"></i>
                        {{ ucfirst($group) }}
                    </h4>
                    <div class="space-y-2">
                        @foreach($groupPermissions as $permission)
                            <label class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                    {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}
                                    class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">{{ $permission->display_name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        @if($permissions->isEmpty())
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-exclamation-circle text-4xl mb-2"></i>
                <p>No permissions available</p>
            </div>
        @endif
    </div>

    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('roles.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded">
            Cancel
        </a>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
            <i class="fas fa-save mr-2"></i>Update Role
        </button>
    </div>
</form>

<script>
function checkAll() {
    document.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function uncheckAll() {
    document.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endsection
