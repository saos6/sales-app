<?php

namespace App\Http\Controllers;

use App\Models\SalesH;
use App\Models\SalesM;
use App\Models\Cust;
use App\Models\Emp;
use App\Models\Item;
use App\Exports\SalesExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesH::with(['cust', 'emp']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('sales_no', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('subject', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('cust_name', 'like', '%' . $request->input('search') . '%');
            });
        }

        if ($request->has('sort')) {
            $direction = $request->input('order', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->latest();
        }

        $perPage = $request->input('per_page', 10);

        return Inertia::render('Sales/Index', [
            'sales' => $query->paginate($perPage)->withQueryString(),
            'filters' => $request->only(['search', 'per_page']),
            'sort' => $request->input('sort'),
            'order' => $request->input('order'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Sales/Create', [
            'custs' => Cust::all(),
            'emps' => Emp::all(),
            'items' => Item::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_no' => 'required|string|max:255|unique:sales_h,sales_no',
            'sales_date' => 'required|date',
            'posting_date' => 'required|date',
            'cust_id' => 'required|exists:custs,id',
            'emps_id' => 'required|exists:emps,id',
            'subject' => 'required|string|max:200',
            'details' => 'required|array',
            'details.*.item_id' => 'required|exists:items,id',
            'details.*.quantity' => 'required|numeric',
            'details.*.unit_price' => 'required|numeric',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $cust = Cust::find($validated['cust_id']);

            $subtotal = 0;
            $tax = 0;

            foreach ($validated['details'] as $detail) {
                $item = Item::find($detail['item_id']);
                $amount = $detail['quantity'] * $detail['unit_price'];
                $tax_rate = 0.10; // Assuming 10% tax rate
                $subtotal += $amount;
                $tax += $amount * $tax_rate;
            }

            $salesH = SalesH::create([
                'sales_no' => $validated['sales_no'],
                'sales_date' => $validated['sales_date'],
                'posting_date' => $validated['posting_date'],
                'cust_id' => $validated['cust_id'],
                'cust_name' => $cust->name,
                'emps_id' => $validated['emps_id'],
                'subject' => $validated['subject'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
                'status' => 'draft',
            ]);

            foreach ($validated['details'] as $index => $detail) {
                $item = Item::find($detail['item_id']);
                $amount = $detail['quantity'] * $detail['unit_price'];
                $tax_rate = 0.10;
                SalesM::create([
                    'sales_id' => $salesH->id,
                    'line_no' => $index + 1,
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'quantity' => $detail['quantity'],
                    'unit' => $item->unit,
                    'unit_price' => $detail['unit_price'],
                    'amount' => $amount,
                    'tax_rate' => $tax_rate,
                    'tax_amount' => $amount * $tax_rate,
                ]);
            }
        });

        return redirect()->route('sales.index')->with('success', '売上を登録しました。');
    }

    public function edit(SalesH $sale)
    {
        return Inertia::render('Sales/Edit', [
            'sale' => $sale->load('details'),
            'custs' => Cust::all(),
            'emps' => Emp::all(),
            'items' => Item::all(),
        ]);
    }

    public function update(Request $request, SalesH $sale)
    {
        $validated = $request->validate([
            'sales_no' => 'required|string|max:255|unique:sales_h,sales_no,' . $sale->id,
            'sales_date' => 'required|date',
            'posting_date' => 'required|date',
            'cust_id' => 'required|exists:custs,id',
            'emps_id' => 'required|exists:emps,id',
            'subject' => 'required|string|max:200',
            'details' => 'required|array',
            'details.*.item_id' => 'required|exists:items,id',
            'details.*.quantity' => 'required|numeric',
            'details.*.unit_price' => 'required|numeric',
        ]);

        DB::transaction(function () use ($validated, $request, $sale) {
            $cust = Cust::find($validated['cust_id']);

            $subtotal = 0;
            $tax = 0;

            foreach ($validated['details'] as $detail) {
                $item = Item::find($detail['item_id']);
                $amount = $detail['quantity'] * $detail['unit_price'];
                $tax_rate = 0.10; // Assuming 10% tax rate
                $subtotal += $amount;
                $tax += $amount * $tax_rate;
            }

            $sale->update([
                'sales_no' => $validated['sales_no'],
                'sales_date' => $validated['sales_date'],
                'posting_date' => $validated['posting_date'],
                'cust_id' => $validated['cust_id'],
                'cust_name' => $cust->name,
                'emps_id' => $validated['emps_id'],
                'subject' => $validated['subject'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
            ]);

            $sale->details()->delete();

            foreach ($validated['details'] as $index => $detail) {
                $item = Item::find($detail['item_id']);
                $amount = $detail['quantity'] * $detail['unit_price'];
                $tax_rate = 0.10;
                SalesM::create([
                    'sales_id' => $sale->id,
                    'line_no' => $index + 1,
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'quantity' => $detail['quantity'],
                    'unit' => $item->unit,
                    'unit_price' => $detail['unit_price'],
                    'amount' => $amount,
                    'tax_rate' => $tax_rate,
                    'tax_amount' => $amount * $tax_rate,
                ]);
            }
        });

        return redirect()->route('sales.index')->with('success', '売上情報を更新しました。');
    }

    public function destroy(SalesH $sale)
    {
        $sale->delete();

        return redirect()->route('sales.index')->with('success', '売上を削除しました。');
    }

    public function export(Request $request)
    {
        return Excel::download(new SalesExport($request->input('search')), 'sales.xlsx');
    }
}
