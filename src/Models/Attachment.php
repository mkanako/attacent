<?php

namespace Cc\Attacent\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachments';
    protected $hidden = [
        'uid',
        'created_at',
        'updated_at',
        'type',
        'path',
    ];
    protected $appends = ['url'];

    public function getPathAttribute()
    {
        return $this->type . '/' . $this->attributes['path'];
    }

    public function getUrlAttribute()
    {
        return resolve('Cc\Attacent\disk')->url($this->path);
    }
}
