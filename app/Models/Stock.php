<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'date_id',

        'quantity',
        'quantity_full',

        'in_way_to_client',
        'in_way_from_client',

        'price',
        'discount',

        'is_supply',
        'is_realization',

        'sc_code',

        'last_change_date',
    ];

    protected $casts = [
        'last_change_date' => 'datetime',

        'is_supply' => 'boolean',
        'is_realization' => 'boolean',

        'price' => 'decimal:2',
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
