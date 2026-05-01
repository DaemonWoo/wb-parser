<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = 'incomes';

    protected $fillable = [
        'income_id',
        'product_id',
        'warehouse_id',
        'date_id',
        'quantity',
        'total_price',
        'date_close',
        'last_change_date',
    ];

    protected $casts = [
        'last_change_date' => 'datetime',
        'date_close' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function dimDate()
    {
        return $this->belongsTo(DimDate::class, 'date_id');
    }
}
