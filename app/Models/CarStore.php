<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
class CarStore extends Model
{
    use HasFactory, softDeletes;
    protected $guarded = [];

    public function setNameAttribute($value){
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'id_city');
    }
   
    public function photos(): HasMany
    {
        return $this->hasMany(StorePhoto::class, 'car_store_id', 'id');
    }
    public function storeServices(): HasMany
    {
        return $this->hasMany(StoreService::class, 'car_store_id', 'id');
    }
}
