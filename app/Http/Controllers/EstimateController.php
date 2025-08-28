<?php

namespace App\Http\Controllers;

use App\Models\EstimateH;
use App\Models\EstimateM;
use App\Models\Cust; // For customer selection in form
use App\Models\Emp;  // For employee selection in form
use App\Models\Item; // For item selection in form
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Exports\EstimatesExport; // Assuming this will be created later
use Maatwebsite\Excel\Facades\Excel; // Assuming Laravel Excel is installed

class EstimateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'per_page', 'sort', 'order']);

        $perPage = $filters['per_page'] ?? 10;
        $sort = $filters['sort'] ?? 'estimate_date';
        $order = $filters['order'] ?? 'desc';
        $search = $filters['search'] ?? '';

        $estimates = EstimateH::query()
            ->when($search, function ($query, $search) {
                $query->where('estimate_no', 'like', '%' . $search . '%')
                      ->orWhere('subject', 'like', '%' . $search . '%')
                      ->orWhere('cust_name', 'like', '%' . $search . '%');
            })
            ->orderBy($sort, $order)
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Estimates/Index', [
            'estimates' => $estimates,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $custs = Cust::all(['id', 'name']);
        $emps = Emp::all(['id', 'name']);
        $items = Item::all();
        return Inertia::render('Estimates/Create', [
            'custs' => $custs,
            'emps' => $emps,
            'items' => $items,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'estimate_no' => 'required|string|max:255|unique:estimate_h,estimate_no',
                'estimate_date' => 'required|date',
                'valid_until' => 'nullable|date|after_or_equal:estimate_date',
                'cust_id' => 'required|exists:custs,id',
                'emps_id' => 'required|exists:emps,id',
                'subject' => 'required|string|max:255',
                'currency' => 'required|string|max:10',
                'status' => 'required|string|max:50',
                'details' => 'array',
                'details.*.item_name' => 'required|string|max:255',
                'details.*.quantity' => 'required|numeric|min:0',
                'details.*.unit_price' => 'required|numeric|min:0',
                'details.*.tax_rate' => 'required|string|max:50',
            ]);

            $cust = Cust::find($request->cust_id);
            $emp = Emp::find($request->emps_id);

            $estimateH = EstimateH::create([
                'estimate_no' => $request->estimate_no,
                'estimate_date' => $request->estimate_date,
                'valid_until' => $request->valid_until,
                'cust_id' => $request->cust_id,
                'cust_name' => $cust->name,
                'cust_department' => $cust->department,
                'cust_person' => $cust->contact_person_name,
                'emps_id' => $request->emps_id,
                'subject' => $request->subject,
                'currency' => $request->currency,
                'remarks' => $request->remarks,
                'status' => $request->status,
                'deleted' => false,
            ]);

            $subtotal = 0;
            $tax = 0;
            foreach ($request->details as $index => $detail) {
                $amount = $detail['quantity'] * $detail['unit_price'];
                $subtotal += $amount;

                // Simple tax calculation for demonstration
                if ($detail['tax_rate'] === '標準') {
                    $tax += $amount * 0.10; // 10% tax
                } elseif ($detail['tax_rate'] === '軽減') {
                    $tax += $amount * 0.08; // 8% tax
                }

                EstimateM::create([
                    'estimate_id' => $estimateH->id,
                    'line_no' => $index + 1,
                    'item_code' => $detail['item_code'],
                    'item_name' => $detail['item_name'],
                    'spec' => $detail['spec'],
                    'quantity' => $detail['quantity'],
                    'unit' => $detail['unit'],
                    'unit_price' => $detail['unit_price'],
                    'amount' => $amount,
                    'tax_rate' => $detail['tax_rate'],
                    'remarks' => $detail['remarks'],
                    'deleted' => false,
                ]);
            }

            $estimateH->subtotal = $subtotal;
            $estimateH->tax = $tax;
            $estimateH->total = $subtotal + $tax;
            $estimateH->save();

            DB::commit();
            return redirect()->route('estimates.index')->with('success', '見積が正常に登録されました。');
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '見積の登録中にエラーが発生しました: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EstimateH $estimate)
    {
        $estimate->load('details'); // Load related estimate_m records
        $custs = Cust::all(['id', 'name']);
        $emps = Emp::all(['id', 'name']);
        $items = Item::all();
        return Inertia::render('Estimates/Edit', [
            'estimate' => $estimate,
            'custs' => $custs,
            'emps' => $emps,
            'items' => $items,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EstimateH $estimate)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'estimate_no' => 'required|string|max:255|unique:estimate_h,estimate_no,' . $estimate->id,
                'estimate_date' => 'required|date',
                'valid_until' => 'nullable|date|after_or_equal:estimate_date',
                'cust_id' => 'required|exists:custs,id',
                'emps_id' => 'required|exists:emps,id',
                'subject' => 'required|string|max:255',
                'currency' => 'required|string|max:10',
                'status' => 'required|string|max:50',
                'details' => 'array',
                'details.*.item_name' => 'required|string|max:255',
                'details.*.quantity' => 'required|numeric|min:0',
                'details.*.unit_price' => 'required|numeric|min:0',
                'details.*.tax_rate' => 'required|string|max:50',
            ]);

            $cust = Cust::find($request->cust_id);
            $emp = Emp::find($request->emps_id);

            $estimate->update([
                'estimate_no' => $request->estimate_no,
                'estimate_date' => $request->estimate_date,
                'valid_until' => $request->valid_until,
                'cust_id' => $request->cust_id,
                'cust_name' => $cust->name,
                'cust_department' => $cust->department,
                'cust_person' => $cust->contact_person_name,
                'emps_id' => $request->emps_id,
                'subject' => $request->subject,
                'currency' => $request->currency,
                'remarks' => $request->remarks,
                'status' => $request->status,
            ]);

            // Sync details: delete old, insert new
            $estimate->details()->delete(); // Simple approach: delete all and re-create
            $subtotal = 0;
            $tax = 0;
            foreach ($request->details as $index => $detail) {
                $amount = $detail['quantity'] * $detail['unit_price'];
                $subtotal += $amount;

                if ($detail['tax_rate'] === '標準') {
                    $tax += $amount * 0.10;
                } elseif ($detail['tax_rate'] === '軽減') {
                    $tax += $amount * 0.08;
                }

                EstimateM::create([
                    'estimate_id' => $estimate->id,
                    'line_no' => $index + 1,
                    'item_code' => $detail['item_code'],
                    'item_name' => $detail['item_name'],
                    'spec' => $detail['spec'],
                    'quantity' => $detail['quantity'],
                    'unit' => $detail['unit'],
                    'unit_price' => $detail['unit_price'],
                    'amount' => $amount,
                    'tax_rate' => $detail['tax_rate'],
                    'remarks' => $detail['remarks'],
                    'deleted' => false,
                ]);
            }

            $estimate->subtotal = $subtotal;
            $estimate->tax = $tax;
            $estimate->total = $subtotal + $tax;
            $estimate->save();

            DB::commit();
            return redirect()->route('estimates.index')->with('success', '見積が正常に更新されました。');
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '見積の更新中にエラーが発生しました: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EstimateH $estimate)
    {
        DB::beginTransaction();
        try {
            $estimate->deleted = true; // Soft delete
            $estimate->save();
            // Optionally, soft delete details as well
            // $estimate->details()->update(['deleted' => true]);

            DB::commit();
            return redirect()->route('estimates.index')->with('success', '見積が正常に削除されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '見積の削除中にエラーが発生しました: ' . $e->getMessage());
        }
    }

    /**
     * Export estimates to Excel.
     */
    public function export(Request $request)
    {
        $search = $request->search ?? '';

        $estimates = EstimateH::query()
            ->when($search, function ($query, $search) {
                $query->where('estimate_no', 'like', '%' . $search . '%')
                      ->orWhere('subject', 'like', '%' . $search . '%')
                      ->orWhere('cust_name', 'like', '%' . $search . '%');
            })
            ->with('details') // Load details for export
            ->get();

        return Excel::download(new EstimatesExport($estimates), 'estimates.xlsx');
    }
}