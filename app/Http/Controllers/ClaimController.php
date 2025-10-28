<?php

namespace App\Http\Controllers;

use App\Exports\ClaimsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ClaimController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->getClaimsQuery($request);

        $claims = $query->paginate($request->input('per_page', 10))
            ->appends($request->except('page'));

        return Inertia::render('Claims/Index', [
            'claims' => $claims,
            'filters' => $request->all(['search', 'per_page', 'sort_by', 'sort_direction']),
        ]);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new ClaimsExport($request), 'claims.xlsx');
    }

    public function exportPdf(Request $request)
    {
        // TODO: Implement PDF export
        return response()->json(['message' => 'PDF export not implemented yet.']);
    }

    public function exportInvoices(Request $request)
    {
        $claimIds = $request->input('claim_ids');
        $claims = DB::table('sales_h')->whereIn('id', $claimIds)->get();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('invoices.bulk', compact('claims'));

        return $pdf->download('invoices.pdf');
    }

    private function getClaimsQuery(Request $request)
    {
        $sales = DB::table('sales_h')
            ->select(
                DB::raw("'売上' as type"),
                'id',
                'sales_no as voucher_no',
                'cust_id',
                'cust_name',
                'sales_date as voucher_date',
                'subtotal',
                'tax',
                'total'
            )
            ->whereNull('deleted_at');

        $payments = DB::table('payments_h')
            ->select(
                DB::raw("'入金' as type"),
                'id',
                'receipt_no as voucher_no',
                'cust_id',
                'cust_name',
                DB::raw('receipt_date as voucher_date'),
                DB::raw('total_amount as subtotal'),
                DB::raw('0 as tax'),
                DB::raw('total_amount as total')
            )
            ->whereNull('deleted_at');

        $unionQuery = $sales->unionAll($payments);
        $query = DB::table(DB::raw("({$unionQuery->toSql()}) as claims"))
            ->mergeBindings($unionQuery);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('cust_name', 'like', "%{$search}%")
                    ->orWhere('voucher_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sort_by')) {
            $sortBy = $request->input('sort_by');
            $sortDirection = $request->input('sort_direction', 'asc');
            if (in_array($sortBy, ['type', 'voucher_no', 'cust_name', 'voucher_date', 'total'])) {
                $query->orderBy($sortBy, $sortDirection);
            }
        } else {
            $query->orderBy('voucher_date', 'desc');
        }

        return $query;
    }
}