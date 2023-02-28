<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class search extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        
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
    // public function scopeSearch($q, $name)
    // {
    //     return $q->where('users_name', 'LIKE', '%' . $name . '%')
    //         ->orwhere('users_code', 'like', '%' . $name . '%')
    //         ->get();;
    // }
    public function search()
    {
        return $this->belongsTo(search::class);
    }
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}



