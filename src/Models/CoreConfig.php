<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\CoreConfig as CoreConfigContract;

class CoreConfig extends Model implements CoreConfigContract
{
    protected $table = 'core_config';

    protected $fillable = [
        'code',
        'value',
        'channel_code',
        'locale_code',
    ];

    protected $hidden = ['token'];
}
