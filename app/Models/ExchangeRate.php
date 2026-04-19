<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $table = 'exchange_rates';
    protected $fillable = ['currency_code', 'rate_to_inr', 'fetched_at'];
    public $timestamps = true;
}
