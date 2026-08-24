<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ScrapeJob extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'scrape_jobs';

    protected $fillable = [
        'latitude',
        'longitude',
        'radius',
        'status',
        'total_found',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius' => 'integer',
        'total_found' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function businesses()
    {
        return $this->hasMany(Business::class, 'scrape_job_id');
    }
}