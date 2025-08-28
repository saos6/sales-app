<?php

namespace App\Exports;

use App\Models\EstimateH;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EstimatesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $estimates;

    public function __construct($estimates)
    {
        $this->estimates = $estimates;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->estimates;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            '見積番号',
            '見積日',
            '有効期限',
            '顧客ID',
            '顧客名',
            '部署名',
            '担当者名',
            '社内担当者ID',
            '件名／案件名',
            '小計',
            '消費税額',
            '合計金額',
            '通貨',
            '備考',
            'ステータス',
            '削除フラグ',
            '作成日時',
            '更新日時',
            // Add headings for details if needed, or create a separate export for details
        ];
    }

    /**
     * @var EstimateH $estimate
     */
    public function map($estimate): array
    {
        return [
            $estimate->id,
            $estimate->estimate_no,
            $estimate->estimate_date,
            $estimate->valid_until,
            $estimate->cust_id,
            $estimate->cust_name,
            $estimate->cust_department,
            $estimate->cust_person,
            $estimate->emps_id,
            $estimate->subject,
            $estimate->subtotal,
            $estimate->tax,
            $estimate->total,
            $estimate->currency,
            $estimate->remarks,
            $estimate->status,
            $estimate->deleted ? 'はい' : 'いいえ',
            $estimate->created_at,
            $estimate->updated_at,
        ];
    }
}
