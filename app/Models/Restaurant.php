<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class Restaurant extends Authenticatable
{
    use HasApiTokens;


    protected $fillable = [
        'name',
        'email',
        'password',
        'slug',
        'status',
        'approved_at',
        'description',
        'address',
        'contact_number',
        'logo'
    ];


    protected $hidden = [
        'password'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($restaurant) {
            $restaurant->slug = Str::slug($restaurant->name) . '-' . uniqid();
        });
    }

    // Use slug for route model binding
    public function getRouteKeyName()
    {
        return 'slug';
    }


    public function documents()
    {
        return $this->hasMany(RestaurantDocuments::class);
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function staff()
    {
        return $this->hasMany(User::class);
    }


}
