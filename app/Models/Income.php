<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function dimDate(): BelongsTo
    {
        return $this->belongsTo(DimDate::class, 'date_id');
    }
}
