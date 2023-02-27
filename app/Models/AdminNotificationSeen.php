<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotificationSeen extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_notification_id',
        'admin_id',
    ];
}
