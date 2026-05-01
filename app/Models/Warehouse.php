<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name'];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
}
