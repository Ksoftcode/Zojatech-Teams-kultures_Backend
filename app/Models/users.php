<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
    ];

    public function user()
    {
        return $this->belongsTo(users::class);
    }
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}

