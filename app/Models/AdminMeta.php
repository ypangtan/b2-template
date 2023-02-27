<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'meta_key',
        'meta_value',
    ];
}
