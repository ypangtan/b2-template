<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSeen extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_notification_id',
        'user_id',
    ];
}
