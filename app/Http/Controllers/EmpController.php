<?php

namespace App\Http\Controllers;

use App\Models\Emp;
use App\Models\Dept;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\EmpsExport;
use Maatwebsite\Excel\Facades\Excel;

class EmpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Emp::with('dept');

        if ($request->filled('search')) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('search') . '%')
                      ->orWhere('email', 'like', '%' . $request->input('search') . '%');
            });
        }

        if ($request->has('sort')) {
            $direction = $request->input('order', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        }

        $perPage = $request->input('per_page', 10);

        return Inertia::render('Emps/Index', [
            'emps' => $query->paginate($perPage)->withQueryString(),
            'filters' => $request->only(['search', 'per_page']),
            'sort' => $request->input('sort'),
            'order' => $request->input('order'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Emps/Create', [
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
            'email' => 'required|string|email|max:255|unique:emps',
            'birthday' => 'required|date',
            'depts_id' => 'nullable|integer|exists:depts,id',
        ]);

        Emp::create($request->all());

        return redirect()->route('emps.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Emp $emp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Emp $emp)
    {
        return Inertia::render('Emps/Edit', [
            'emp' => $emp,
            'depts' => Dept::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Emp $emp)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:emps,email,'.$emp->id,
            'birthday' => 'required|date',
            'depts_id' => 'nullable|integer|exists:depts,id',
        ]);

        $emp->update($request->all());

        return redirect()->route('emps.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Emp $emp)
    {
        $emp->delete();

        return redirect()->route('emps.index');
    }

    /**
     * Export data to Excel.
     */
    public function export(Request $request)
    {
        return Excel::download(new EmpsExport($request->input('search')), 'emps.xlsx');
    }
}
