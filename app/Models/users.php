<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class users extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    protected $guarded = ['id'];

    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function user()
    {
        return $this->belongsTo(users::class);
    }
    protected $casts = [
       'email_verified_at' => 'datetime',
    ];
    // public function sendPasswordResetNotification($token)
    // {

    //     $url = 'https://spa.test/reset-password?token=' . $token;

    //     $this->notify(new ResetPasswordNotification($url));
    // }
}
