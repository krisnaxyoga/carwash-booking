<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreService extends Model
{
    use HasFactory, softDeletes;
    protected $guarded = [];

    public function carStore(): BelongsTo
    {
        return $this->belongsTo(CarStore::class, 'car_store_id', 'id');
    }

    public function carService(): BelongsTo
    {
        return $this->belongsTo(CarService::class, 'car_service_id', 'id');
    }
}
