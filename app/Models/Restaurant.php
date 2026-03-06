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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function staff()
    {
        return $this->hasMany(User::class);
    }

     public function staffAttendances()
    {
        return $this->hasMany(StaffAttendance::class);
    }


    // SaaS Relationships

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
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

    public function currentSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->whereIn('status', ['trial', 'active'])
            ->latestOfMany(); // latestOfMany() ensures you get the newest active one.
    }

    public function latestExpiredSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'expired')
            ->latestOfMany();
    }

    public function getFeatureLimit(string $featureName)
    {
        $subscription = $this->currentSubscription;

        if (!$subscription) {
            return 0;
        }

        $plan = $subscription->plan;

        if (!$plan) {
            return 0;
        }

        $feature = $plan->features()
            ->where('name', $featureName)
            ->first();

        if (!$feature) {
            return 0;
        }

        return (int) $feature->pivot->value;
    }

    public function hasBooleanFeature(string $featureName): bool
    {
        $subscription = $this->currentSubscription;

        if (!$subscription) {
            return false;
        }

        $feature = $subscription->plan
            ->features()
            ->where('name', $featureName)
            ->first();

        return $feature && $feature->pivot->value === 'true';
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

