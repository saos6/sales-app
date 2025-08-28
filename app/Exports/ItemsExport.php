<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;

    public function __construct($search)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = Item::query();

        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                    ->orWhere('item_name', 'like', "%{$search}%")
                    ->orWhere('spec', 'like', "%{$search}%")
                    ->orWhere('maker', 'like', "%{$search}%")
                    ->orWhere('jan_code', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            '商品コード',
            '商品名',
            '仕様',
            'カテゴリ',
            '単位',
            '標準単価',
            '原価',
            '税率',
            '通貨',
            'メーカー',
            'JANコード',
            '在庫数量',
            '発注点',
            '納期',
            '状態',
            '備考',
            '作成日時',
            '更新日時',
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->item_code,
            $item->item_name,
            $item->spec,
            method_exists($item->category_id, 'label') ? $item->category_id->label() : $item->category_id,
            $item->unit,
            $item->standard_price,
            $item->cost_price,
            $item->tax_rate,
            $item->currency,
            $item->maker,
            $item->jan_code,
            $item->stock_qty,
            $item->reorder_point,
            $item->lead_time,
            $item->status,
            $item->remarks,
            $item->created_at,
            $item->updated_at,
        ];
    }
}


