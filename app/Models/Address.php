<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'address';

    protected $fillable = [
        'address',
        'city',
        'state',
        'pincode',
        'user_id',
    ];

    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}
public function customer()
{
    return $this->belongsTo(customer::class, 'user_id');
}

public function orders()
{
    return $this->hasMany(Order::class, 'address_id');
}
}
