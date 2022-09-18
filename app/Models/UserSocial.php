<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSocial extends Model
{
    use HasFactory;

    protected $table = 'users_social';

    protected $fillable = [
        'user_id',
        'platform',
        'identifier',
        'uuid',
    ];
}
