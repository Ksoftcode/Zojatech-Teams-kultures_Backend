<?php

namespace App\Models;

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
    // public function firstname()
    // {
    //     return $this->belongsTo(fiesrname::class);
    // }
    // public function lastname()
    // {
    //     return $this->belongsTo(lastname::class);
    // }
    // public function username()

    // {
    //     return $this->belongsTo(username::class);
    // }
    // public function email()
    // {
    //     return $this->belongsTo(email::class);
    // }
    // public function scopeSearchUsers($q, $name)
    // {
    //     return $q->where('users_name', 'LIKE', '%' . $name . '%')
    //         ->orwhere('users_code', 'like', '%' . $name . '%')
    //         ->get();;
    // }
    public function user()
    {
        return $this->belongsTo(users::class);
    }
    protected $casts = [
       'email_verified_at' => 'datetime',
    ];
}
