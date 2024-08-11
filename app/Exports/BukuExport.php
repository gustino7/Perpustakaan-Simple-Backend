<?php

namespace App\Exports;

use App\Models\Buku;
use Maatwebsite\Excel\Concerns\FromCollection;

class BukuExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Buku::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Judul',
            'User ID',
            'Kategori ID',
            'Deskripsi',
            'Jumlah',
            'File',
            'Cover',
            'Created At',
            'Updated At'
        ];
    }
}
