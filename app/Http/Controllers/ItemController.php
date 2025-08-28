<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Exports\ItemsExport;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%")
                  ->orWhere('spec', 'like', "%{$search}%")
                  ->orWhere('maker', 'like', "%{$search}%")
                  ->orWhere('jan_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('sort')) {
            $direction = $request->input('order', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->latest();
        }

        $perPage = $request->input('per_page', 10);

        return Inertia::render('Items/Index', [
            'items' => $query->paginate($perPage)->withQueryString(),
            'filters' => $request->only(['search', 'per_page']),
            'sort' => $request->input('sort'),
            'order' => $request->input('order'),
            'categoryOptions' => collect(CategoryType::cases())->map(fn($c) => ['value' => $c->value, 'label' => $c->label()])->values(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Items/Create', [
            'categoryOptions' => collect(CategoryType::cases())->map(fn($c) => ['value' => $c->value, 'label' => $c->label()])->values(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:255|unique:items,item_code',
            'item_name' => 'required|string|max:255',
            'spec' => 'nullable|string|max:255',
            'category_id' => 'required|integer',
            'unit' => 'required|string|max:50',
            'standard_price' => 'nullable|numeric',
            'cost_price' => 'nullable|numeric',
            'tax_rate' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'maker' => 'nullable|string|max:255',
            'jan_code' => 'nullable|string|max:255',
            'stock_qty' => 'nullable|integer',
            'reorder_point' => 'nullable|integer',
            'lead_time' => 'nullable|integer',
            'status' => 'required|string|max:50',
            'remarks' => 'nullable|string',
        ]);

        Item::create($validated);

        return redirect()->route('items.index')->with('success', '商品を登録しました。');
    }

    public function edit(Item $item)
    {
        return Inertia::render('Items/Edit', [
            'item' => $item,
            'categoryOptions' => collect(CategoryType::cases())->map(fn($c) => ['value' => $c->value, 'label' => $c->label()])->values(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:255|unique:items,item_code,' . $item->id,
            'item_name' => 'required|string|max:255',
            'spec' => 'nullable|string|max:255',
            'category_id' => 'required|integer',
            'unit' => 'required|string|max:50',
            'standard_price' => 'nullable|numeric',
            'cost_price' => 'nullable|numeric',
            'tax_rate' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'maker' => 'nullable|string|max:255',
            'jan_code' => 'nullable|string|max:255',
            'stock_qty' => 'nullable|integer',
            'reorder_point' => 'nullable|integer',
            'lead_time' => 'nullable|integer',
            'status' => 'required|string|max:50',
            'remarks' => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')->with('success', '商品を更新しました。');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', '商品を削除しました。');
    }

    public function export(Request $request)
    {
        return Excel::download(new ItemsExport($request->input('search')), 'items.xlsx');
    }
}


