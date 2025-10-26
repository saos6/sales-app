<?php

namespace App\Exports;

use App\Models\SalesH;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $search;

    public function __construct($search)
    {
        $this->search = $search;
    }

    public function query()
    {
        $query = SalesH::with(['cust', 'emp']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('sales_no', 'like', '%' . $this->search . '%')
                    ->orWhere('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('cust_name', 'like', '%' . $this->search . '%');
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            '売上番号',
            '売上日',
            '計上日',
            '顧客名',
            '件名',
            '小計',
            '消費税額',
            '合計金額',
            '担当者',
            'ステータス',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->sales_no,
            $sale->sales_date,
            $sale->posting_date,
            $sale->cust_name,
            $sale->subject,
            $sale->subtotal,
            $sale->tax,
            $sale->total,
            $sale->emp->name,
            $sale->status,
        ];
    }
}
