<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'restaurant_id',
        'order_id',
        'table_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'service_charge',
        'total_amount',
        'payment_method',
        'payment_status',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // 🔗 Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            $invoice->invoice_number = self::generateInvoiceNumber();
        });
    }

    private static function generateInvoiceNumber(): string
    {
        $year = now()->year;

        $latest = self::whereYear('created_at', $year)->latest()->first();

        $number = $latest
            ? (int) substr($latest->invoice_number, -5)
            : 0;

        return 'INV-' . $year . '-' . str_pad($number + 1, 5, '0', STR_PAD_LEFT);
    }

    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';

    public function isPaid()
    {
        return $this->payment_status === self::STATUS_PAID;
    }
}