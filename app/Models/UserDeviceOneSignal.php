<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDeviceOneSignal extends Model
{
    use HasFactory;

    protected $table = 'users_device_os';

    protected $fillable = [
        'register_token',
        'device_type',
        'user_id',
    ];
}
