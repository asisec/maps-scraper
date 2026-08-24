<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BusinessesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected Collection $businesses;

    public function __construct(Collection $businesses)
    {
        $this->businesses = $businesses;
    }

    public function collection(): Collection
    {
        return $this->businesses;
    }

    public function headings(): array
    {
        return [
            'İşletme Adı',
            'Açık Adres',
            'Ortalama Puan',
            'Yorum Sayısı',
            'Telefon',
            'E-posta',
            'Web Sitesi',
            'Enlem',
            'Boylam',
        ];
    }

    public function map($business): array
    {
        return [
            $business->name ?? '',
            $business->address ?? '',
            $business->rating ?? '',
            $business->reviews_count ?? '',
            $business->phone ?? '',
            $business->email ?? '',
            $business->website ?? '',
            $business->latitude ?? '',
            $business->longitude ?? '',
        ];
    }
}