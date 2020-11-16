<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\Currency as CurrencyContract;

class Currency extends Model implements CurrencyContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'symbol',
    ];

    /**
     * Set currency code in capital
     *
     * @param $code
     * @return void
     */
    public function setCodeAttribute($code)
    {
        $this->attributes['code'] = strtoupper($code);
    }

    /**
     * Get the exchange rate associated with the currency.
     */
    public function exchange_rate()
    {
        return $this->hasOne(CurrencyExchangeRateProxy::modelClass(), 'target_currency');
    }
}
