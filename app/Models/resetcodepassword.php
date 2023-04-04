<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class resetcodepassword extends Model
{
    use HasFactory;
    const UPDATED_AT = null;
    protected $table = 'reset_code_password';
    protected $fillable = [
        'email',
        'Token',
        'created_at',
    ];
}
