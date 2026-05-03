<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $table = 'sales';

    protected $fillable = [
        'sale_id',

        'product_id',
        'warehouse_id',
        'date_id',

        'g_number',
        'spp',

        'oblast_okrug_name',
        'country_name',
        'region_name',

        'total_price',
        'finished_price',
        'price_with_disc',
        'for_pay',
        'discount_percent',

        'is_storno',
        'is_supply',
        'is_realization',

        'income_id',

        'last_change_date',
    ];

    protected $casts = [
        'last_change_date' => 'datetime',

        'is_storno' => 'boolean',
        'is_supply' => 'boolean',
        'is_realization' => 'boolean',

        'total_price' => 'decimal:2',
        'finished_price' => 'decimal:2',
        'price_with_disc' => 'decimal:2',
        'for_pay' => 'decimal:2',

        'spp' => 'integer',
        'discount_percent' => 'integer',
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
