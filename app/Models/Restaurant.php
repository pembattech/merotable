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
        'owner_name',
        'email',
        'password',
        'slug',
        'contact_number',
        'status',
        'description',
        'address',
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

    public function setting()
    {
        return $this->hasOne(RestaurantSetting::class);
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


    // SaaS Relationships

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    // public function addOns(): HasMany
    // {
    //     return $this->hasMany(RestaurantAddOn::class);
    // }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SaaS Feature Logic
    |--------------------------------------------------------------------------
    */

    public function getFeatureLimit(string $featureName): int
    {
        $plan = $this->subscription?->plan;

        if (!$plan)
            return 0;

        $feature = $plan->features()
            ->where('name', $featureName)
            ->first();

        if (!$feature)
            return 0;

        $baseLimit = (int) $feature->pivot->value;

        $extra = $this->addOns()
            ->whereHas('addOn', function ($q) use ($featureName) {
                $q->where('feature_key', $featureName);
            })
            ->where('expires_at', '>', now())
            ->sum('quantity');

        return $baseLimit + $extra;
    }

    public function hasBooleanFeature(string $featureName): bool
    {
        $plan = $this->subscription?->plan;

        if (!$plan)
            return false;

        $feature = $plan->features()
            ->where('name', $featureName)
            ->first();

        if (!$feature)
            return false;

        return $feature->pivot->value === 'true';
    }

    // Add to Restaurant model — replaces the old hasFeature()
    public function canAddMoreTables(): bool
    {
        $limit = $this->getFeatureLimit('tables_limit');
        return $limit === 0 || $this->tables()->count() < $limit;
    }

    public function remainingTables(): int
    {
        $limit = $this->getFeatureLimit('tables_limit');
        if ($limit === 0)
            return PHP_INT_MAX; // unlimited
        return max(0, $limit - $this->tables()->count());
    }
}

