<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\ScrapeJob;
use App\Services\ScraperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OAT;

class ScraperController extends Controller
{
    protected ScraperService $scraperService;

    public function __construct(ScraperService $scraperService)
    {
        $this->scraperService = $scraperService;
    }

    #[OAT\Post(
        path: '/api/scrape',
        summary: 'Harita uzerinden isletme verisi kazima',
        description: 'Belirtilen koordinat ve yari cap icindeki isletmeleri Google Haritalar uzerinden tarar ve veritabanina kaydeder.',
        tags: ['Tarama Islemleri'],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(
                required: ['latitude', 'longitude', 'radius'],
                properties: [
                    new OAT\Property(property: 'latitude', description: 'Merkez enlem derecesi (-90 ile 90 arasi)', type: 'number', format: 'float', example: 39.9334),
                    new OAT\Property(property: 'longitude', description: 'Merkez boylam derecesi (-180 ile 180 arasi)', type: 'number', format: 'float', example: 32.8597),
                    new OAT\Property(property: 'radius', description: 'Metre cinsinden dairesel tarama yari capi', type: 'integer', example: 1000),
                ]
            )
        ),
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Tarama basariyla tamamlandi',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'status', type: 'boolean', example: true),
                        new OAT\Property(property: 'message', type: 'string', example: 'Tarama islemi basariyla tamamlandi.'),
                        new OAT\Property(property: 'job_id', type: 'string', example: '66c9f28d8b9e1a2b3c4d5e6f'),
                        new OAT\Property(property: 'total', type: 'integer', example: 15),
                        new OAT\Property(property: 'data', type: 'array', items: new OAT\Items(type: 'object'))
                    ]
                )
            ),
            new OAT\Response(
                response: 422,
                description: 'Gecersiz parametreler',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'status', type: 'boolean', example: false),
                        new OAT\Property(property: 'message', type: 'string', example: 'Lutfen gecerli koordinat ve yari cap bilgisi giriniz.'),
                        new OAT\Property(property: 'errors', type: 'object')
                    ]
                )
            ),
            new OAT\Response(
                response: 500,
                description: 'Sunucu hatasi',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'status', type: 'boolean', example: false),
                        new OAT\Property(property: 'message', type: 'string', example: 'Tarama sirasinda bir sunucu hatasi olustu.')
                    ]
                )
            )
        ]
    )]
    public function scrape(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['required', 'integer', 'min:10', 'max:50000'],
        ], [
            'latitude.required' => 'Enlem koordinati zorunludur.',
            'latitude.numeric' => 'Enlem gecerli bir sayi olmalidir.',
            'latitude.between' => 'Enlem degeri -90 ile 90 arasinda olmalidir.',
            'longitude.required' => 'Boylam koordinati zorunludur.',
            'longitude.numeric' => 'Boylam gecerli bir sayi olmalidir.',
            'longitude.between' => 'Boylam degeri -180 ile 180 arasinda olmalidir.',
            'radius.required' => 'Tarama yari capi zorunludur.',
            'radius.integer' => 'Yari cap bir tam sayi olmalidir.',
            'radius.min' => 'Yari cap en az 10 metre olmalidir.',
            'radius.max' => 'Yari cap en fazla 50.000 metre olabilir.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Lutfen gecerli koordinat ve yari cap bilgisi giriniz.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $latitude = (float) $request->input('latitude');
            $longitude = (float) $request->input('longitude');
            $radius = (int) $request->input('radius');

            $result = $this->scraperService->execute($latitude, $longitude, $radius);

            return response()->json([
                'status' => true,
                'message' => 'Tarama islemi basariyla tamamlandi.',
                'job_id' => $result['job_id'],
                'total' => $result['total'],
                'data' => $result['data'],
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => false,
                'message' => 'Tarama sirasinda bir sunucu hatasi olustu: ' . $exception->getMessage(),
            ], 500);
        }
    }

    #[OAT\Get(
        path: '/api/businesses',
        summary: 'Kayitli isletmeleri listeleme',
        description: 'Veritabaninda kayitli olan isletmeleri listeler. Istege bagli olarak tarama gorevine gore filtrelenebilir.',
        tags: ['Isletme Verileri'],
        parameters: [
            new OAT\Parameter(
                name: 'job_id',
                description: 'Tarama gorevi kimlik numarasi (ID)',
                in: 'query',
                required: false,
                schema: new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name: 'limit',
                description: 'Dondurulecek maksimum kayit sayisi (varsayilan: 100)',
                in: 'query',
                required: false,
                schema: new OAT\Schema(type: 'integer', default: 100)
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Isletmeler basariyla getirildi',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'status', type: 'boolean', example: true),
                        new OAT\Property(property: 'total', type: 'integer', example: 25),
                        new OAT\Property(property: 'data', type: 'array', items: new OAT\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function businesses(Request $request): JsonResponse
    {
        $query = Business::query()->orderBy('created_at', 'desc');

        if ($request->has('job_id') && !empty($request->input('job_id'))) {
            $query->where('scrape_job_id', $request->input('job_id'));
        }

        $limit = min(500, max(1, (int) $request->input('limit', 100)));
        $businesses = $query->limit($limit)->get();

        return response()->json([
            'status' => true,
            'total' => $businesses->count(),
            'data' => $businesses,
        ]);
    }

    #[OAT\Get(
        path: '/api/jobs/{id}',
        summary: 'Tarama gorevi durumu sorgulama',
        description: 'Belirtilen kimlik numarasina sahip tarama gorevinin durumunu ve sonucunu getirir.',
        tags: ['Tarama Islemleri'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                description: 'Gorev kimlik numarasi',
                in: 'path',
                required: true,
                schema: new OAT\Schema(type: 'string')
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Gorev bilgisi bulundu',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'status', type: 'boolean', example: true),
                        new OAT\Property(property: 'data', type: 'object')
                    ]
                )
            ),
            new OAT\Response(
                response: 404,
                description: 'Gorev bulunamadi',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'status', type: 'boolean', example: false),
                        new OAT\Property(property: 'message', type: 'string', example: 'Belirtilen tarama gorevi bulunamadi.')
                    ]
                )
            )
        ]
    )]
    public function job(string $id): JsonResponse
    {
        $job = ScrapeJob::find($id);

        if (!$job) {
            return response()->json([
                'status' => false,
                'message' => 'Belirtilen tarama gorevi bulunamadi.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $job,
        ]);
    }
}