<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\BookingProductDefaultSlot as BookingProductDefaultSlotContract;

class BookingProductDefaultSlot extends Model implements BookingProductDefaultSlotContract
{
    protected $table = 'booking_product_default_slots';

    public $timestamps = false;

    protected $casts = ['slots' => 'array'];

    protected $fillable = [
        'booking_type',
        'duration',
        'break_time',
        'slots',
        'booking_product_id'
    ];

    /**
     * Get the product that owns the attribute value.
     */
    public function booking_product()
    {
        return $this->belongsTo(BookingProductProxy::modelClass());
    }
}
