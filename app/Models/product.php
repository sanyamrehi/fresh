<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;

    protected $table = 'products';//db table

    protected $fillable = [
        'name', 'color', 'size', 'category', 'image', 'price', 'status',
    ];

    // Relationship with size
    public function size()
    {
        return $this->belongsTo(Size::class,'size_id');
    }
    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

public function addresses()
{
    return $this->hasMany(Address::class, 'user_id');
}
public function cartItem()
{
    return $this->belongsTo(Cart::class, 'product_id');
}


}
