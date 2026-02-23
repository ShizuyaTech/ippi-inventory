<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use App\Helpers\ExcelHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('supplier');
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }
        
        $users = $query->latest()->paginate(15)->withQueryString();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('supplier_name')->get();
        return view('users.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,staff,supplier',
        ];

        // Tambahkan validasi supplier_id jika role = supplier
        if ($request->role === 'supplier') {
            $rules['supplier_id'] = 'required|exists:suppliers,id';
        } else {
            $rules['supplier_id'] = 'nullable|exists:suppliers,id';
        }

        $validated = $request->validate($rules);

        // Jika role bukan supplier, supplier_id harus null
        if ($validated['role'] !== 'supplier') {
            $validated['supplier_id'] = null;
        }

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('supplier');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('supplier_name')->get();
        return view('users.edit', compact('user', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,staff,supplier',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ];

        $validated = $request->validate($rules);

        // Update fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        
        // Update password jika diisi
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        // Handle supplier_id berdasarkan role
        if ($validated['role'] === 'supplier') {
            // Gunakan nilai dari $validated yang sudah divalidasi
            $user->supplier_id = $validated['supplier_id'] ?? null;
        } else {
            $user->supplier_id = null;
        }
        
        // Handle status aktif
        $user->is_active = $request->has('is_active') ? 1 : 0;
        
        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Tidak bisa menghapus diri sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus user yang sedang login');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }

    public function export()
    {
        $users = User::all();
        
        $data = $users->map(function($user) {
            return [
                $user->name,
                $user->email,
                ucfirst($user->role),
                $user->supplier ? $user->supplier->supplier_name : '-',
                $user->is_active ? 'Active' : 'Inactive',
                $user->created_at->format('Y-m-d H:i'),
            ];
        })->toArray();
        
        $headers = [
            'Name',
            'Email',
            'Role',
            'Supplier',
            'Status',
            'Created At',
        ];
        
        return ExcelHelper::export($data, $headers, 'users_' . date('YmdHis') . '.xlsx');
    }

    public function exportPdf()
    {
        $users = User::with('supplier')->get();
        $pdf = Pdf::loadView('pdf.users', compact('users'));
        return $pdf->download('users_' . date('YmdHis') . '.pdf');
    }
}
