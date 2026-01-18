<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Restaurant extends Authenticatable
{
    use HasApiTokens;


    protected $fillable = [
        'name',
        'email',
        'password',
        'status'
    ];


    protected $hidden = [
        'password'
    ];


    public function documents()
{
    return $this->hasMany(RestaurantDocuments::class);
}
}
