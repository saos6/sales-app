<?php

namespace App\Http\Controllers;

use App\Enums\CustomerRank;
use App\Enums\CustomerType;
use App\Enums\IndustryType;
use App\Enums\PaymentTerms;
use App\Exports\CustsExport;
use App\Models\Cust;
use App\Models\Dept;
use App\Models\Emp;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class CustController extends Controller
{
    public function index(Request $request)
    {
        $query = Cust::with(['dept', 'emp']);

        if ($request->filled('search')) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('search') . '%')
                      ->orWhere('name_kana', 'like', '%' . $request->input('search') . '%')
                      ->orWhere('tel', 'like', '%' . $request->input('search') . '%')
                      ->orWhere('email', 'like', '%' . $request->input('search') . '%');
            });
        }

        if ($request->has('sort')) {
            $direction = $request->input('order', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->latest();
        }

        $perPage = $request->input('per_page', 10);

        return Inertia::render('Custs/Index', [
            'custs' => $query->paginate($perPage)->withQueryString(),
            'filters' => $request->only(['search', 'per_page']),
            'sort' => $request->input('sort'),
            'order' => $request->input('order'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Custs/Create', [
            'customerTypes' => array_map(fn($e) => ['value' => $e->value, 'label' => $e->label()], CustomerType::cases()),
            'industryTypes' => array_map(fn($e) => ['value' => $e->value, 'label' => $e->label()], IndustryType::cases()),
            'customerRanks' => array_map(fn($e) => ['value' => $e->value, 'label' => $e->label()], CustomerRank::cases()),
            'paymentTerms' => array_map(fn($e) => ['value' => $e->value, 'label' => $e->label()], PaymentTerms::cases()),
            'depts' => Dept::all(),
            'emps' => Emp::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_kana' => 'nullable|string|max:255',
            'department_name' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'prefecture' => 'nullable|string|max:255',
            'address_line' => 'nullable|string|max:255',
            'tel' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website_url' => 'nullable|url|max:255',
            'customer_type' => 'nullable|integer',
            'industry_type' => 'nullable|integer',
            'first_trade_date' => 'nullable|date',
            'customer_rank' => 'nullable|integer',
            'payment_terms' => 'nullable|integer',
            'depts_id' => 'nullable|exists:depts,id',
            'emps_id' => 'nullable|exists:emps,id',
            'remarks' => 'nullable|string',
        ]);

        Cust::create($validated);

        return redirect()->route('custs.index')->with('success', '顧客を登録しました。');
    }

    public function edit(Cust $cust)
    {
        return Inertia::render('Custs/Edit', [
            'cust' => $cust,
            'customerTypes' => array_map(fn($e) => ['value' => $e->value, 'label' => $e->label()], CustomerType::cases()),
            'industryTypes' => array_map(fn($e) => ['value' => $e->value, 'label' => $e->label()], IndustryType::cases()),
            'customerRanks' => array_map(fn($e) => ['value' => $e->value, 'label' => $e->label()], CustomerRank::cases()),
            'paymentTerms' => array_map(fn($e) => ['value' => $e->value, 'label' => $e->label()], PaymentTerms::cases()),
            'depts' => Dept::all(),
            'emps' => Emp::all(),
        ]);
    }

    public function update(Request $request, Cust $cust)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_kana' => 'nullable|string|max:255',
            'department_name' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'prefecture' => 'nullable|string|max:255',
            'address_line' => 'nullable|string|max:255',
            'tel' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website_url' => 'nullable|url|max:255',
            'customer_type' => 'nullable|integer',
            'industry_type' => 'nullable|integer',
            'first_trade_date' => 'nullable|date',
            'customer_rank' => 'nullable|integer',
            'payment_terms' => 'nullable|integer',
            'depts_id' => 'nullable|exists:depts,id',
            'emps_id' => 'nullable|exists:emps,id',
            'remarks' => 'nullable|string',
        ]);

        $cust->update($validated);

        return redirect()->route('custs.index')->with('success', '顧客情報を更新しました。');
    }

    public function destroy(Cust $cust)
    {
        $cust->delete();

        return redirect()->route('custs.index')->with('success', '顧客を削除しました。');
    }

    public function export(Request $request)
    {
        return Excel::download(new CustsExport($request->input('search')), 'custs.xlsx');
    }
}