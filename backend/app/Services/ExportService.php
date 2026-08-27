<?php

namespace App\Services;

use App\Exports\BusinessesExport;
use App\Models\Business;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ExportService
{
    public function getBusinesses(?string $jobId = null): Collection
    {
        $query = Business::query()->orderBy('created_at', 'desc');
        if (!empty($jobId)) {
            $query->where('scrape_job_id', $jobId);
        }
        return $query->get();
    }

    public function exportXlsx(?string $jobId = null): BinaryFileResponse
    {
        ini_set('memory_limit', '512M');
        $businesses = $this->getBusinesses($jobId);
        $fileName = 'isletmeler_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new BusinessesExport($businesses), $fileName);
    }

    public function exportPdf(?string $jobId = null): Response
    {
        ini_set('memory_limit', '512M');
        $businesses = $this->getBusinesses($jobId);
        $generatedAt = Carbon::now()->format('d.m.Y H:i:s');
        $fileName = 'isletmeler_' . Carbon::now()->format('Ymd_His') . '.pdf';

        $pdf = Pdf::loadView('exports.businesses_pdf', [
            'businesses' => $businesses,
            'generatedAt' => $generatedAt,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }

    public function exportImage(?string $jobId = null): Response
    {
        ini_set('memory_limit', '512M');
        $businesses = $this->getBusinesses($jobId);
        $generatedAt = Carbon::now()->format('d.m.Y H:i:s');
        $fileName = 'isletmeler_' . Carbon::now()->format('Ymd_His') . '.png';

        $imageBinary = $this->generateTableImage($businesses, $generatedAt);

        return response($imageBinary, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    protected function generateTableImage(Collection $businesses, string $generatedAt): string
    {
        $displayBusinesses = $businesses->take(50);
        $width = 1200;
        $rowHeight = 36;
        $headerHeight = 110;
        $footerHeight = 40;
        $rowCount = max(1, $displayBusinesses->count());
        $height = $headerHeight + ($rowCount * $rowHeight) + $footerHeight;

        $image = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($image, 245, 247, 250);
        $white = imagecolorallocate($image, 255, 255, 255);
        $headerBg = imagecolorallocate($image, 25, 118, 210);
        $thBg = imagecolorallocate($image, 38, 50, 56);
        $textDark = imagecolorallocate($image, 33, 33, 33);
        $textMuted = imagecolorallocate($image, 117, 117, 117);
        $textWhite = imagecolorallocate($image, 255, 255, 255);
        $border = imagecolorallocate($image, 224, 224, 224);
        $rowAlt = imagecolorallocate($image, 238, 242, 246);
        $starColor = imagecolorallocate($image, 245, 127, 23);

        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        imagefilledrectangle($image, 0, 0, $width, 60, $headerBg);
        imagestring($image, 5, 20, 15, 'Harita Kaziyici - Isletme Listesi', $textWhite);
        imagestring($image, 3, 20, 38, 'Olusturulma: ' . $generatedAt . ' | Toplam Isletme: ' . $businesses->count(), $textWhite);

        $thY = 70;
        imagefilledrectangle($image, 10, $thY, $width - 10, $thY + 30, $thBg);

        $cols = [
            ['title' => '#', 'x' => 20, 'w' => 40],
            ['title' => 'Isletme Adi', 'x' => 65, 'w' => 240],
            ['title' => 'Acik Adres', 'x' => 310, 'w' => 340],
            ['title' => 'Puan', 'x' => 660, 'w' => 90],
            ['title' => 'Telefon', 'x' => 760, 'w' => 160],
            ['title' => 'E-posta', 'x' => 930, 'w' => 250],
        ];

        foreach ($cols as $col) {
            imagestring($image, 4, $col['x'], $thY + 7, $col['title'], $textWhite);
        }

        $y = 102;
        if ($displayBusinesses->isEmpty()) {
            imagefilledrectangle($image, 10, $y, $width - 10, $y + $rowHeight, $white);
            imagerectangle($image, 10, $y, $width - 10, $y + $rowHeight, $border);
            imagestring($image, 3, 500, $y + 10, 'Kayitli isletme bulunamadi.', $textMuted);
            $y += $rowHeight;
        } else {
            foreach ($displayBusinesses as $index => $business) {
                $rowBg = ($index % 2 === 0) ? $white : $rowAlt;
                imagefilledrectangle($image, 10, $y, $width - 10, $y + $rowHeight, $rowBg);
                imagerectangle($image, 10, $y, $width - 10, $y + $rowHeight, $border);

                $name = $this->truncateText((string) ($business->name ?? '-'), 28);
                $address = $this->truncateText((string) ($business->address ?? '-'), 42);
                $rating = $business->rating ? ($business->rating . ' Yildiz') : '-';
                $phone = $this->truncateText((string) ($business->phone ?? '-'), 18);
                $email = $this->truncateText((string) ($business->email ?? '-'), 28);

                imagestring($image, 3, 20, $y + 10, (string) ($index + 1), $textDark);
                imagestring($image, 4, 65, $y + 10, $name, $textDark);
                imagestring($image, 3, 310, $y + 10, $address, $textDark);
                imagestring($image, 3, 660, $y + 10, $rating, $starColor);
                imagestring($image, 3, 760, $y + 10, $phone, $textDark);
                imagestring($image, 3, 930, $y + 10, $email, $textDark);

                $y += $rowHeight;
            }
        }

        imagestring($image, 2, 20, $y + 12, 'Harita Kaziyici tarafindan uretilmistir.', $textMuted);

        ob_start();
        imagepng($image);
        $binary = ob_get_clean();
        imagedestroy($image);

        return $binary;
    }

    protected function truncateText(string $text, int $length): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length - 3) . '...';
    }
}