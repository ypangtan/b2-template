<?php

namespace App\Models;

use DateTimeInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileManager extends Model
{
    use HasFactory;
    
    protected $hidden = [
        'id'
    ];

    protected $appends = [
        'encrypted_id',
    ];

    protected $fillable = [
        'document_id',
        'file',
        'path',
        'type',
        'status',
    ];

    public function document() {
        return $this->belongsTo( Document::class, 'document_id' );
    }

    public function getEncryptedIdAttribute() {
        return \Helper::encode( $this->attributes['id'] );
    }

    protected function serializeDate( DateTimeInterface $date ) {
        return $date->timezone( 'Asia/Kuala_Lumpur' )->format( 'Y-m-d H:i:s' );
    }
}
