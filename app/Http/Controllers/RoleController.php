<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('display_name')->get()->groupBy('group');
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles|alpha_dash',
            'display_name' => 'required',
            'description' => 'nullable',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            if (isset($validated['permissions'])) {
                $role->permissions()->sync($validated['permissions']);
            }

            DB::commit();
            return redirect()->route('roles.index')
                ->with('success', 'Role berhasil ditambahkan dengan ' . count($validated['permissions'] ?? []) . ' permissions');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan role: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Role $role)
    {
        $role->load(['permissions' => function($query) {
            $query->orderBy('group')->orderBy('display_name');
        }]);
        $permissionsByGroup = $role->permissions->groupBy('group');
        
        return view('roles.show', compact('role', 'permissionsByGroup'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->orderBy('display_name')->get()->groupBy('group');
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|alpha_dash|unique:roles,name,' . $role->id,
            'display_name' => 'required',
            'description' => 'nullable',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
            ]);

            $role->permissions()->sync($validated['permissions'] ?? []);

            DB::commit();
            return redirect()->route('roles.index')
                ->with('success', 'Role berhasil diupdate dengan ' . count($validated['permissions'] ?? []) . ' permissions');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate role: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Role $role)
    {
        // Prevent deletion if role has users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus role yang masih digunakan oleh ' . $role->users()->count() . ' user(s)');
        }

        // Prevent deletion of default roles
        if (in_array($role->name, ['admin', 'staff', 'supplier'])) {
            return back()->with('error', 'Tidak dapat menghapus role default sistem');
        }

        try {
            $role->delete();
            return redirect()->route('roles.index')
                ->with('success', 'Role berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus role: ' . $e->getMessage());
        }
    }

    /**
     * Show permissions management page
     */
    public function permissions()
    {
        $permissions = Permission::orderBy('group')->orderBy('display_name')->get()->groupBy('group');
        $roles = Role::with('permissions')->get();
        
        return view('roles.permissions', compact('permissions', 'roles'));
    }

    /**
     * Update role status
     */
    public function toggleStatus(Role $role)
    {
        $role->is_active = !$role->is_active;
        $role->save();

        $status = $role->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Role {$role->display_name} berhasil {$status}");
    }
}
