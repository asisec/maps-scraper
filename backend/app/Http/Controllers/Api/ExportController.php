<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExportService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OAT;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    #[OAT\Get(
        path: '/api/export/excel',
        summary: 'Isletmeleri Excel (XLSX) formatinda indirme',
        description: 'Kayitli tum isletmeleri veya belirli bir goreve ait isletmeleri XLSX formatinda disa aktarir.',
        tags: ['Disa Aktarma Islemleri'],
        parameters: [
            new OAT\Parameter(
                name: 'job_id',
                description: 'Tarama gorev kimlik numarasi',
                in: 'query',
                required: false,
                schema: new OAT\Schema(type: 'string')
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Excel dosyasi basariyla olusturuldu',
                content: new OAT\MediaType(
                    mediaType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                )
            )
        ]
    )]
    public function excel(Request $request): BinaryFileResponse
    {
        $jobId = $request->query('job_id');
        return $this->exportService->exportXlsx($jobId);
    }

    #[OAT\Get(
        path: '/api/export/pdf',
        summary: 'Isletmeleri PDF formatinda indirme',
        description: 'Kayitli tum isletmeleri veya belirli bir goreve ait isletmeleri PDF formatinda disa aktarir.',
        tags: ['Disa Aktarma Islemleri'],
        parameters: [
            new OAT\Parameter(
                name: 'job_id',
                description: 'Tarama gorev kimlik numarasi',
                in: 'query',
                required: false,
                schema: new OAT\Schema(type: 'string')
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'PDF dosyasi basariyla olusturuldu',
                content: new OAT\MediaType(
                    mediaType: 'application/pdf'
                )
            )
        ]
    )]
    public function pdf(Request $request): Response
    {
        $jobId = $request->query('job_id');
        return $this->exportService->exportPdf($jobId);
    }

    #[OAT\Get(
        path: '/api/export/image',
        summary: 'Isletmeleri Resim (PNG) formatinda indirme',
        description: 'Kayitli tum isletmeleri veya belirli bir goreve ait isletmeleri PNG gorsel tablosu olarak disa aktarir.',
        tags: ['Disa Aktarma Islemleri'],
        parameters: [
            new OAT\Parameter(
                name: 'job_id',
                description: 'Tarama gorev kimlik numarasi',
                in: 'query',
                required: false,
                schema: new OAT\Schema(type: 'string')
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'PNG resmi basariyla olusturuldu',
                content: new OAT\MediaType(
                    mediaType: 'image/png'
                )
            )
        ]
    )]
    public function image(Request $request): Response
    {
        $jobId = $request->query('job_id');
        return $this->exportService->exportImage($jobId);
    }
}