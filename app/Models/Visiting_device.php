<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visiting_device extends Model
{
    use HasFactory;

    protected $table = "visiting_device";

    protected $fillable = [
        'userId',
        'device',
        'deviceType',
        'platform',
        'platform_version',
        'browser',
        'browser_version',
    ];

    public function user () 
    {
        return $this->belongsTo(
            User::class,
            'userId',
            'userId'
        );
    }
}