<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'feature_plans')
                    ->withPivot('value')
                    ->withTimestamps();
    }
}
