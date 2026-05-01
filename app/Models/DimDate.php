<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimDate extends Model
{
    protected $table = 'dim_dates';

    protected $fillable = [
        'date',
        'year',
        'month',
        'day',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'date_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'date_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'date_id');
    }

    public function incomes()
    {
        return $this->hasMany(Income::class, 'date_id');
    }
}
