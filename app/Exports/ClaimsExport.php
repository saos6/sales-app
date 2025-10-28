<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClaimsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $sales = DB::table('sales_h')
            ->select(
                DB::raw("'売上' as type"),
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

        if ($this->request->filled('search')) {
            $search = $this->request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('cust_name', 'like', "%{$search}%")
                    ->orWhere('voucher_no', 'like', "%{$search}%");
            });
        }

        if ($this->request->filled('sort_by')) {
            $sortBy = $this->request->input('sort_by');
            $sortDirection = $this->request->input('sort_direction', 'asc');
            if (in_array($sortBy, ['type', 'voucher_no', 'cust_name', 'voucher_date', 'total'])) {
                $query->orderBy($sortBy, $sortDirection);
            }
        } else {
            $query->orderBy('voucher_date', 'desc');
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            '区分',
            '伝票番号',
            '顧客ID',
            '顧客名',
            '日付',
            '税抜金額',
            '消費税',
            '税込金額',
        ];
    }

    public function map($claim): array
    {
        return [
            $claim->type,
            $claim->voucher_no,
            $claim->cust_id,
            $claim->cust_name,
            $claim->voucher_date,
            $claim->subtotal,
            $claim->tax,
            $claim->total,
        ];
    }
}
