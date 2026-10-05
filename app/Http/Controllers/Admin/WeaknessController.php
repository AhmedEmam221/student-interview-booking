<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Weakness;
use Illuminate\Http\Request;

class WeaknessController extends Controller
{
    public function index()
    {
        $weaknesses = Weakness::withCount('applications')->orderBy('name')->paginate(15);
        return view('admin.weaknesses.index', compact('weaknesses'));
    }

    public function create()
    {
        return view('admin.weaknesses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:weaknesses,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Weakness::create($request->validated());
        return redirect()->route('admin.weaknesses.index')
            ->with('success', 'Weakness created successfully.');
    }

    public function show(Weakness $weakness)
    {
        $weakness->loadCount('applications');
        return view('admin.weaknesses.show', compact('weakness'));
    }

    public function edit(Weakness $weakness)
    {
        return view('admin.weaknesses.edit', compact('weakness'));
    }

    public function update(Request $request, Weakness $weakness)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:weaknesses,name,' . $weakness->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $weakness->update($request->validated());
        return redirect()->route('admin.weaknesses.index')
            ->with('success', 'Weakness updated successfully.');
    }

    public function destroy(Weakness $weakness)
    {
        if ($weakness->applications()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete weakness with associated applications. Consider deactivating instead.']);
        }
        
        $weakness->delete();
        return redirect()->route('admin.weaknesses.index')
            ->with('success', 'Weakness deleted successfully.');
    }
}