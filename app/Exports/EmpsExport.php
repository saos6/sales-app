<?php

namespace App\Exports;

use App\Models\Emp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmpsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Emp::with('dept')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            '氏名',
            'Email',
            '生年月日',
            '所属',
            '作成日時',
            '更新日時',
        ];
    }

    public function map($emp): array
    {
        return [
            $emp->id,
            $emp->name,
            $emp->email,
            $emp->birthday,
            $emp->dept?->name,
            $emp->created_at,
            $emp->updated_at,
        ];
    }
}
