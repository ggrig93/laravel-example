<?php

namespace Modules\Cart\Models;

use Database\Factories\CartProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Models\Product;

class CartProduct extends Model
{
    use HasFactory;

    /**
     * @return CartProductFactory
     */
    protected static function newFactory(): CartProductFactory
    {
        return CartProductFactory::new();
    }

    /**
     * @var string
     */
    protected $table = 'cart_products';

    /**
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'cart_id',
        'company_id',
        'quantity',
        'is_promo',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
}
