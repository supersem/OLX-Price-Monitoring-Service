<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Factories\Factory;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'current_price', 'last_checked_at'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
