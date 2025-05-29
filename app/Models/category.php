<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    use HasFactory;

    protected $table = 'categories';//db table

    protected $fillable = [
        'category',
        'status',
    ];
    // Relationship with product

        public function products()
        {
            return $this->hasMany(Product::class);
        }


}
