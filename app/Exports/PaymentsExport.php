<?php

namespace App\Exports;

use App\Models\PaymentH;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $search;

    public function __construct($search)
    {
        $this->search = $search;
    }

    public function query()
    {
        $query = PaymentH::with(['cust', 'emp']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('payment_no', 'like', '%' . $this->search . '%')
                    ->orWhere('remarks', 'like', '%' . $this->search . '%')
                    ->orWhere('cust_name', 'like', '%' . $this->search . '%');
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            '入金番号',
            '入金日',
            '顧客名',
            '備考',
            '合計金額',
            '担当者',
            'ステータス',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->payment_no,
            $payment->payment_date,
            $payment->cust_name,
            $payment->remarks,
            $payment->total_amount,
            $payment->emp->name,
            $payment->status,
        ];
    }
}
