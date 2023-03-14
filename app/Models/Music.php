<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'afrobeat',
        'world',
        'juju',
    ];
    public function user()
    {
        return $this->belongsTo(Music::class);
    }
}
