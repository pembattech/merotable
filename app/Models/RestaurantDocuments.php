<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantDocuments extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'document_type',
        'document_path',
        'status',
        'remarks',
        'verified_at'
    ];


        public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

}
