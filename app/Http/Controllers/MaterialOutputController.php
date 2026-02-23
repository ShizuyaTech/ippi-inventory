<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialOutput;
use Illuminate\Http\Request;

class MaterialOutputController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Material $material)
    {
        $validated = $request->validate([
            'output_material_id' => 'required|exists:materials,id',
            'quantity_per_unit' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        // Check if output already exists
        $exists = MaterialOutput::where('source_material_id', $material->id)
            ->where('output_material_id', $validated['output_material_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Output material sudah ditambahkan sebelumnya');
        }

        $validated['source_material_id'] = $material->id;

        MaterialOutput::create($validated);

        return back()->with('success', 'Material output berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaterialOutput $output)
    {
        $validated = $request->validate([
            'quantity_per_unit' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        // Handle checkbox - only present when checked
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $output->update($validated);

        return back()->with('success', 'Material output berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialOutput $output)
    {
        $output->delete();

        return back()->with('success', 'Material output berhasil dihapus');
    }
}
