<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OriginLog extends Model
{
    protected $fillable = [
        'method',
        'params',
        'data',
        'uri',
        'remote_addr',
        'user_agent',
        'header'
    ];
    protected $casts = [
        'method'      => 'string',
        'params'      => 'array',
        'data'        => 'array',
        'uri'         => 'string',
        'remote_addr' => 'string',
        'user_agent'  => 'string',
        'header'      => 'array',
    ];
}
