<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Business extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'businesses';

    protected $fillable = [
        'name',
        'address',
        'rating',
        'reviews_count',
        'phone',
        'email',
        'website',
        'latitude',
        'longitude',
        'place_id',
        'scrape_job_id',
    ];

    protected $casts = [
        'rating' => 'float',
        'reviews_count' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function scrapeJob()
    {
        return $this->belongsTo(ScrapeJob::class, 'scrape_job_id');
    }
}