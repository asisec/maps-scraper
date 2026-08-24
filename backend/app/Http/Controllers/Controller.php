<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OAT;
use OpenApi\Annotations as OA;

#[OAT\Info(
    version: "1.0.0",
    title: "Harita Kaziyici API Dokumantasyonu",
    description: "Google Haritalar isletme verisi kazima ve disa aktarma REST API dokumantasyonu.",
    contact: new OAT\Contact(email: "bilgi@haritakaziyici.com")
)]
#[OAT\Server(
    url: "http://localhost:8000",
    description: "Birincil API Sunucusu"
)]
/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Harita Kaziyici API Dokumantasyonu",
 *     description="Google Haritalar isletme verisi kazima ve disa aktarma REST API dokumantasyonu.",
 *     @OA\Contact(
 *         email="bilgi@haritakaziyici.com"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Birincil API Sunucusu"
 * )
 */
abstract class Controller
{
}