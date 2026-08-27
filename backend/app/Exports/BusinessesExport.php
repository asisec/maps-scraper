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
            'Tüm Telefonlar',
            'E-posta',
            'Tüm E-postalar',
            'Web Sitesi',
            'WhatsApp',
            'Sosyal Medya',
            'Enlem',
            'Boylam',
        ];
    }

    public function map($business): array
    {
        $phones = is_array($business->phones) ? implode(', ', $business->phones) : ($business->phone ?? '');
        $emails = is_array($business->emails) ? implode(', ', $business->emails) : ($business->email ?? '');
        $social = '';
        if (is_array($business->social_links)) {
            $socialParts = [];
            foreach ($business->social_links as $platform => $link) {
                $socialParts[] = ucfirst($platform) . ': ' . $link;
            }
            $social = implode(' | ', $socialParts);
        }

        return [
            $business->name ?? '',
            $business->address ?? '',
            $business->rating ?? '',
            $business->reviews_count ?? '',
            $business->phone ?? '',
            $phones,
            $business->email ?? '',
            $emails,
            $business->website ?? '',
            $business->whatsapp ?? '',
            $social,
            $business->latitude ?? '',
            $business->longitude ?? '',
        ];
    }
}