<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class size extends Model
{
    use HasFactory;

    protected $table = 'sizes';//db table

    protected $fillable = [
        'size',
        'status',
    ];
    // Relationship with product
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
