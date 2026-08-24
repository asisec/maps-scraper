<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\ScrapeJob;
use Tests\TestCase;

class ScraperApiTest extends TestCase
{
    public function test_scrape_validation_fails_with_invalid_parameters(): void
    {
        $response = $this->postJson('/api/scrape', [
            'latitude' => 150.0,
            'longitude' => 200.0,
            'radius' => 5,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'message' => 'Lutfen gecerli koordinat ve yari cap bilgisi giriniz.',
            ])
            ->assertJsonStructure(['errors' => ['latitude', 'longitude', 'radius']]);
    }

    public function test_scrape_endpoint_creates_job_and_returns_businesses(): void
    {
        $response = $this->postJson('/api/scrape', [
            'latitude' => 39.9334,
            'longitude' => 32.8597,
            'radius' => 1000,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Tarama islemi basariyla tamamlandi.',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'job_id',
                'total',
                'data',
            ]);
    }

    public function test_businesses_endpoint_returns_saved_records(): void
    {
        Business::create([
            'name' => 'Test Restoran',
            'address' => 'Test Mah. No:1',
            'rating' => 4.5,
            'reviews_count' => 50,
            'phone' => '+90 555 123 4567',
            'email' => 'test@restoran.com',
            'website' => 'https://testrestoran.com',
            'latitude' => 39.9334,
            'longitude' => 32.8597,
            'place_id' => 'test_place_1',
            'scrape_job_id' => 'test_job_1',
        ]);

        $response = $this->getJson('/api/businesses');

        $response->assertStatus(200)
            ->assertJson(['status' => true])
            ->assertJsonStructure(['status', 'total', 'data']);
    }

    public function test_job_endpoint_returns_job_details(): void
    {
        $job = ScrapeJob::create([
            'latitude' => 39.9334,
            'longitude' => 32.8597,
            'radius' => 1000,
            'status' => 'completed',
            'total_found' => 5,
        ]);

        $response = $this->getJson('/api/jobs/' . (string) $job->_id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'status' => 'completed',
                    'total_found' => 5,
                ],
            ]);
    }

    public function test_export_excel_endpoint(): void
    {
        $response = $this->get('/api/export/excel');
        $response->assertStatus(200);
    }

    public function test_export_pdf_endpoint(): void
    {
        $response = $this->get('/api/export/pdf');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_image_endpoint(): void
    {
        $response = $this->get('/api/export/image');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'image/png');
    }
}