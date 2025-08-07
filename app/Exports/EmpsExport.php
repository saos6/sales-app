<?php

namespace App\Exports;

use App\Models\Emp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmpsExport implements FromCollection, WithHeadings, WithMapping
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
        $query = Emp::with('dept');

        if (!empty($this->search)) {
            $query->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
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
