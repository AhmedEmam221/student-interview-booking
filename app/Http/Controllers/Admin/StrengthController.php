<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Strength;
use Illuminate\Http\Request;

class StrengthController extends Controller
{
    public function index()
    {
        $strengths = Strength::withCount('applications')->orderBy('name')->paginate(15);
        return view('admin.strengths.index', compact('strengths'));
    }

    public function create()
    {
        return view('admin.strengths.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:strengths,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Strength::create($request->validated());
        return redirect()->route('admin.strengths.index')
            ->with('success', 'Strength created successfully.');
    }

    public function show(Strength $strength)
    {
        $strength->loadCount('applications');
        return view('admin.strengths.show', compact('strength'));
    }

    public function edit(Strength $strength)
    {
        return view('admin.strengths.edit', compact('strength'));
    }

    public function update(Request $request, Strength $strength)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:strengths,name,' . $strength->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $strength->update($request->validated());
        return redirect()->route('admin.strengths.index')
            ->with('success', 'Strength updated successfully.');
    }

    public function destroy(Strength $strength)
    {
        if ($strength->applications()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete strength with associated applications. Consider deactivating instead.']);
        }
        
        $strength->delete();
        return redirect()->route('admin.strengths.index')
            ->with('success', 'Strength deleted successfully.');
    }
}