<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('group')->nullable(); // Group permissions (e.g., 'materials', 'users', 'transactions')
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create role_permissions pivot table
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id']);
        });

        // Add role_id to users table (nullable for migration)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained()->onDelete('restrict');
        });

        // Seed default roles
        $this->seedDefaultRoles();
        
        // Seed default permissions
        $this->seedDefaultPermissions();
        
        // Assign permissions to roles
        $this->assignDefaultPermissions();
        
        // Migrate existing users to new role system
        $this->migrateExistingUsers();
    }

    /**
     * Seed default roles
     */
    private function seedDefaultRoles(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full access to all system features',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Access to transactions and production orders',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'supplier',
                'display_name' => 'Supplier',
                'description' => 'Limited access for suppliers',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Seed default permissions
     */
    private function seedDefaultPermissions(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'view-dashboard', 'display_name' => 'View Dashboard', 'group' => 'dashboard'],
            ['name' => 'view-all-stock', 'display_name' => 'View All Stock Data', 'group' => 'dashboard'],
            
            // Materials
            ['name' => 'view-materials', 'display_name' => 'View Materials', 'group' => 'materials'],
            ['name' => 'create-materials', 'display_name' => 'Create Materials', 'group' => 'materials'],
            ['name' => 'edit-materials', 'display_name' => 'Edit Materials', 'group' => 'materials'],
            ['name' => 'delete-materials', 'display_name' => 'Delete Materials', 'group' => 'materials'],
            ['name' => 'export-materials', 'display_name' => 'Export Materials', 'group' => 'materials'],
            ['name' => 'import-materials', 'display_name' => 'Import Materials', 'group' => 'materials'],
            
            // Suppliers
            ['name' => 'view-suppliers', 'display_name' => 'View Suppliers', 'group' => 'suppliers'],
            ['name' => 'create-suppliers', 'display_name' => 'Create Suppliers', 'group' => 'suppliers'],
            ['name' => 'edit-suppliers', 'display_name' => 'Edit Suppliers', 'group' => 'suppliers'],
            ['name' => 'delete-suppliers', 'display_name' => 'Delete Suppliers', 'group' => 'suppliers'],
            ['name' => 'export-suppliers', 'display_name' => 'Export Suppliers', 'group' => 'suppliers'],
            ['name' => 'import-suppliers', 'display_name' => 'Import Suppliers', 'group' => 'suppliers'],
            
            // Customers
            ['name' => 'view-customers', 'display_name' => 'View Customers', 'group' => 'customers'],
            ['name' => 'create-customers', 'display_name' => 'Create Customers', 'group' => 'customers'],
            ['name' => 'edit-customers', 'display_name' => 'Edit Customers', 'group' => 'customers'],
            ['name' => 'delete-customers', 'display_name' => 'Delete Customers', 'group' => 'customers'],
            ['name' => 'export-customers', 'display_name' => 'Export Customers', 'group' => 'customers'],
            ['name' => 'import-customers', 'display_name' => 'Import Customers', 'group' => 'customers'],
            
            // Warehouses
            ['name' => 'view-warehouses', 'display_name' => 'View Warehouses', 'group' => 'warehouses'],
            ['name' => 'create-warehouses', 'display_name' => 'Create Warehouses', 'group' => 'warehouses'],
            ['name' => 'edit-warehouses', 'display_name' => 'Edit Warehouses', 'group' => 'warehouses'],
            ['name' => 'delete-warehouses', 'display_name' => 'Delete Warehouses', 'group' => 'warehouses'],
            ['name' => 'export-warehouses', 'display_name' => 'Export Warehouses', 'group' => 'warehouses'],
            ['name' => 'import-warehouses', 'display_name' => 'Import Warehouses', 'group' => 'warehouses'],
            
            // Transactions
            ['name' => 'view-transactions', 'display_name' => 'View Transactions', 'group' => 'transactions'],
            ['name' => 'create-transactions', 'display_name' => 'Create Transactions', 'group' => 'transactions'],
            ['name' => 'edit-transactions', 'display_name' => 'Edit Transactions', 'group' => 'transactions'],
            ['name' => 'delete-transactions', 'display_name' => 'Delete Transactions', 'group' => 'transactions'],
            ['name' => 'export-transactions', 'display_name' => 'Export Transactions', 'group' => 'transactions'],
            
            // Production Orders
            ['name' => 'view-production-orders', 'display_name' => 'View Production Orders', 'group' => 'production'],
            ['name' => 'create-production-orders', 'display_name' => 'Create Production Orders', 'group' => 'production'],
            ['name' => 'edit-production-orders', 'display_name' => 'Edit Production Orders', 'group' => 'production'],
            ['name' => 'delete-production-orders', 'display_name' => 'Delete Production Orders', 'group' => 'production'],
            ['name' => 'start-production-orders', 'display_name' => 'Start Production Orders', 'group' => 'production'],
            ['name' => 'complete-production-orders', 'display_name' => 'Complete Production Orders', 'group' => 'production'],
            ['name' => 'cancel-production-orders', 'display_name' => 'Cancel Production Orders', 'group' => 'production'],
            
            // Stock Opname
            ['name' => 'view-opname', 'display_name' => 'View Stock Opname', 'group' => 'opname'],
            ['name' => 'create-opname', 'display_name' => 'Create Stock Opname', 'group' => 'opname'],
            ['name' => 'approve-opname', 'display_name' => 'Approve Stock Opname', 'group' => 'opname'],
            
            // Users
            ['name' => 'view-users', 'display_name' => 'View Users', 'group' => 'users'],
            ['name' => 'create-users', 'display_name' => 'Create Users', 'group' => 'users'],
            ['name' => 'edit-users', 'display_name' => 'Edit Users', 'group' => 'users'],
            ['name' => 'delete-users', 'display_name' => 'Delete Users', 'group' => 'users'],
            ['name' => 'export-users', 'display_name' => 'Export Users', 'group' => 'users'],
            
            // Roles & Permissions
            ['name' => 'view-roles', 'display_name' => 'View Roles', 'group' => 'roles'],
            ['name' => 'create-roles', 'display_name' => 'Create Roles', 'group' => 'roles'],
            ['name' => 'edit-roles', 'display_name' => 'Edit Roles', 'group' => 'roles'],
            ['name' => 'delete-roles', 'display_name' => 'Delete Roles', 'group' => 'roles'],
            ['name' => 'manage-permissions', 'display_name' => 'Manage Permissions', 'group' => 'roles'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission['name'],
                'display_name' => $permission['display_name'],
                'group' => $permission['group'],
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Assign default permissions to roles
     */
    private function assignDefaultPermissions(): void
    {
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $staffRole = DB::table('roles')->where('name', 'staff')->first();
        $supplierRole = DB::table('roles')->where('name', 'supplier')->first();

        // Get all permissions
        $allPermissions = DB::table('permissions')->get();

        // Admin gets all permissions
        $adminRolePermissions = [];
        foreach ($allPermissions as $permission) {
            $adminRolePermissions[] = [
                'role_id' => $adminRole->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('role_permissions')->insert($adminRolePermissions);

        // Staff gets limited permissions
        $staffPermissions = [
            'view-dashboard', 'view-all-stock',
            'view-transactions', 'create-transactions', 'edit-transactions', 'export-transactions',
            'view-production-orders', 'create-production-orders', 'edit-production-orders',
            'start-production-orders', 'complete-production-orders',
            'view-opname', 'create-opname',
        ];

        foreach ($staffPermissions as $permName) {
            $permission = DB::table('permissions')->where('name', $permName)->first();
            if ($permission) {
                DB::table('role_permissions')->insert([
                    'role_id' => $staffRole->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Supplier gets very limited permissions
        $supplierPermissions = ['view-dashboard', 'view-transactions'];
        
        foreach ($supplierPermissions as $permName) {
            $permission = DB::table('permissions')->where('name', $permName)->first();
            if ($permission) {
                DB::table('role_permissions')->insert([
                    'role_id' => $supplierRole->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Migrate existing users from enum to role_id
     */
    private function migrateExistingUsers(): void
    {
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $staffRole = DB::table('roles')->where('name', 'staff')->first();
        $supplierRole = DB::table('roles')->where('name', 'supplier')->first();

        DB::table('users')->where('role', 'admin')->update(['role_id' => $adminRole->id]);
        DB::table('users')->where('role', 'staff')->update(['role_id' => $staffRole->id]);
        DB::table('users')->where('role', 'supplier')->update(['role_id' => $supplierRole->id]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
        
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
