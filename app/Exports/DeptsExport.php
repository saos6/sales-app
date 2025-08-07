<?php

namespace App\Exports;

use App\Models\Dept;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DeptsExport implements FromCollection, WithHeadings
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
        $query = Dept::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return $query->get();
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
