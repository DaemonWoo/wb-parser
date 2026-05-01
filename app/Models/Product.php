<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'nm_id',
        'barcode',
        'supplier_article',
        'tech_size',
        'brand',
        'category',
        'subject',
    ];

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
