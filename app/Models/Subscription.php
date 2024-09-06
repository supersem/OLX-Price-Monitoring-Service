<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['ad_id', 'email', 'status', 'verification_token'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
