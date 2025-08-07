<?php

namespace App\Exports;

use App\Models\Cust;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;

    public function __construct($search)
    {
        $this->search = $search;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Cust::with(['dept', 'emp']);

        if (!empty($this->search)) {
            $query->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('name_kana', 'like', '%' . $this->search . '%')
                    ->orWhere('tel', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            '名称',
            '名称カナ',
            '部署名',
            '担当者名',
            '郵便番号',
            '都道府県',
            '市区町村以降の住所',
            '電話番号',
            'FAX番号',
            'メールアドレス',
            'ホームページURL',
            '顧客区分',
            '業種',
            '取引開始日',
            '顧客ランク',
            '支払条件',
            '所属',
            '社員',
            '備考',
            '作成日時',
            '更新日時',
        ];
    }

    public function map($cust): array
    {
        return [
            $cust->id,
            $cust->name,
            $cust->name_kana,
            $cust->department_name,
            $cust->contact_person_name,
            $cust->postal_code,
            $cust->prefecture,
            $cust->address_line,
            $cust->tel,
            $cust->fax,
            $cust->email,
            $cust->website_url,
            $cust->customer_type?->label(),
            $cust->industry_type?->label(),
            $cust->first_trade_date,
            $cust->customer_rank?->label(),
            $cust->payment_terms?->label(),
            $cust->dept?->name,
            $cust->emp?->name,
            $cust->remarks,
            $cust->created_at,
            $cust->updated_at,
        ];
    }
}
