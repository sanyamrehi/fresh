<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory,HasApiTokens;

    protected $table = 'orders';

    protected $fillable = [
        'product_id',
        'payment_id',
        'product_name',
        'price',
        'tax',
        'total_price',
        'user_id',
        'address_id',
    ];

    public function products()
{
    return $this->hasMany(Product::class,'product_id'); // Assuming each user has many products
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function customer()
{
    return $this->belongsTo(customer::class, 'user_id');
}

public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class,'size_id');
    }
    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

}
