<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\BookingProductEventTicketTranslation as BookingProductEventTicketTranslationContract;

class BookingProductEventTicketTranslation extends Model implements BookingProductEventTicketTranslationContract
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];
}
