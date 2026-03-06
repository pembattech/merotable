<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class StaffAttendance extends Model
{
    protected $fillable = [
        'staff_id',
        'restaurant_id',
        'date',
        'check_in',
        'check_out',
        'total_hours',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'total_hours' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function staff()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function countPresent($restaurantId)
    {
        return $this->where('restaurant_id', $restaurantId)
            ->whereDate('date', today())
            ->count();
    }

    // Auto calculate total hours
    public function calculateTotalHours()
    {
        if ($this->check_in && $this->check_out) {
            $minutes = $this->check_in->diffInMinutes($this->check_out);
            $this->total_hours = round($minutes / 60, 2);
            $this->save();
        }
    }

    // Mark as late (you can improve logic later)
    public function markLate($shiftStartTime)
    {
        if ($this->check_in && $this->check_in->format('H:i:s') > $shiftStartTime) {
            $this->status = 'late';
            $this->save();
        }
    }
}