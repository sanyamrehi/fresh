<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class customer extends Model implements Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    use \Illuminate\Auth\Authenticatable;

    protected $table = 'customer';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'password',
        'confirm_password',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
