<?php

namespace Modules\Cart\Models;

use App\Models\Company;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\User\Models\User;
use Database\Factories\CartFactory;

class Cart extends Model
{
    use HasFactory;

    /**
     * @return CartFactory
     */
    protected static function newFactory(): CartFactory
    {
        return CartFactory::new();
    }

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'address_id',
        'company_id',
        'delivery_type',
        'delivery_address',
        'delivery_address_classified',
        'address',
        'homeNumber',
        'building',
        'entrance',
        'intercom',
        'floor',
        'apartment',
        'comment',
        'promo_code',
        'delay_time'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasMany
     */
    public function cartProduct(): HasMany
    {
        return $this->hasMany(CartProduct::class);
    }

    /**
     * @return BelongsTo
     */
    public function userAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'address_id', 'id');
    }
}
