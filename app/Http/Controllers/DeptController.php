<?php

namespace App\Http\Controllers;

use App\Models\Dept;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\DeptsExport;
use Maatwebsite\Excel\Facades\Excel;

class DeptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Depts/Index', [
            'depts' => Dept::with('parent')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Depts/Create', [
            'depts' => Dept::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:depts,id',
        ]);

        Dept::create($request->all());

        return redirect()->route('depts.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Dept $dept)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dept $dept)
    {
        return Inertia::render('Depts/Edit', [
            'dept' => $dept,
            'depts' => Dept::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dept $dept)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:depts,id',
        ]);

        $dept->update($request->all());

        return redirect()->route('depts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dept $dept)
    {
        $dept->delete();

        return redirect()->route('depts.index');
    }

    /**
     * Export data to Excel.
     */
    public function export()
    {
        return Excel::download(new DeptsExport, 'depts.xlsx');
    }
}