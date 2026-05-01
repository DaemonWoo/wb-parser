<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'g_number',
        'product_id',
        'warehouse_id',
        'date_id',

        'date',
        'last_change_date',

        'total_price',
        'discount_percent',

        'region_name',

        'is_cancel',
        'cancel_dt',

        'income_id',
    ];

    protected $casts = [
        'date' => 'datetime',
        'cancel_dt' => 'datetime',
        'last_change_date' => 'datetime',

        'is_cancel' => 'boolean',
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
