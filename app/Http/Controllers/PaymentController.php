<?php

namespace App\Http\Controllers;

use App\Models\PaymentH;
use App\Models\PaymentM;
use App\Models\Cust;
use App\Models\Emp;
use App\Exports\PaymentsExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Enums\PaymentCategory;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentH::with(['cust', 'emp']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('payment_no', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('remarks', 'like', '%' . $request->input('search') . '%')
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

        return Inertia::render('Payments/Index', [
            'payments' => $query->paginate($perPage)->withQueryString(),
            'filters' => $request->only(['search', 'per_page']),
            'sort' => $request->input('sort'),
            'order' => $request->input('order'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Payments/Create', [
            'custs' => Cust::all(),
            'emps' => Emp::all(),
            'paymentCategories' => collect(PaymentCategory::cases())->map(function ($case) {
                return ['value' => $case->value, 'label' => $case->label()];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'cust_id' => 'required|exists:custs,id',
            'emps_id' => 'required|exists:emps,id',
            'remarks' => 'nullable|string|max:200',
            'details' => 'required|array',
            'details.*.amount' => 'required|numeric',
            'details.*.payment_category' => ['required', Rule::in(array_column(PaymentCategory::cases(), 'value'))],
            'details.*.bank_info' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $cust = Cust::find($validated['cust_id']);

            $total_amount = 0;

            foreach ($validated['details'] as $detail) {
                $total_amount += $detail['amount'];
            }

            $paymentH = PaymentH::create([
                'payment_no' => 'PAY-' . time(), // Simple unique number
                'payment_date' => $validated['payment_date'],
                'cust_id' => $validated['cust_id'],
                'cust_name' => $cust->name,
                'emps_id' => $validated['emps_id'],
                'remarks' => $validated['remarks'],
                'total_amount' => $total_amount,
                'status' => 'draft',
            ]);

            foreach ($validated['details'] as $index => $detail) {
                PaymentM::create([
                    'payment_id' => $paymentH->id,
                    'line_no' => $index + 1,
                    'payment_category' => $detail['payment_category'],
                    'bank_info' => $detail['bank_info'],
                    'amount' => $detail['amount'],
                ]);
            }
        });

        return redirect()->route('payments.index')->with('success', '入金を登録しました。');
    }

    public function edit(PaymentH $payment)
    {
        return Inertia::render('Payments/Edit', [
            'payment' => $payment->load('details'),
            'custs' => Cust::all(),
            'emps' => Emp::all(),
            'paymentCategories' => collect(PaymentCategory::cases())->map(function ($case) {
                return ['value' => $case->value, 'label' => $case->label()];
            }),
        ]);
    }

    public function update(Request $request, PaymentH $payment)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'cust_id' => 'required|exists:custs,id',
            'emps_id' => 'required|exists:emps,id',
            'remarks' => 'nullable|string|max:200',
            'details' => 'required|array',
            'details.*.amount' => 'required|numeric',
            'details.*.payment_category' => ['required', Rule::in(array_column(PaymentCategory::cases(), 'value'))],
            'details.*.bank_info' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated, $request, $payment) {
            $cust = Cust::find($validated['cust_id']);

            $total_amount = 0;

            foreach ($validated['details'] as $detail) {
                $total_amount += $detail['amount'];
            }

            $payment->update([
                'payment_date' => $validated['payment_date'],
                'cust_id' => $validated['cust_id'],
                'cust_name' => $cust->name,
                'emps_id' => $validated['emps_id'],
                'remarks' => $validated['remarks'],
                'total_amount' => $total_amount,
            ]);

            $payment->details()->delete();

            foreach ($validated['details'] as $index => $detail) {
                PaymentM::create([
                    'payment_id' => $payment->id,
                    'line_no' => $index + 1,
                    'payment_category' => $detail['payment_category'],
                    'bank_info' => $detail['bank_info'],
                    'amount' => $detail['amount'],
                ]);
            }
        });

        return redirect()->route('payments.index')->with('success', '入金情報を更新しました。');
    }

    public function destroy(PaymentH $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index')->with('success', '入金を削除しました。');
    }

    public function export(Request $request)
    {
        return Excel::download(new PaymentsExport($request->input('search')), 'payments.xlsx');
    }
}
