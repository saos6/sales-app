<?php

namespace App\Exports;

use App\Models\Dept;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DeptsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Dept::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            '所属名',
            '親所属ID',
            '作成日時',
            '更新日時',
        ];
    }
}
