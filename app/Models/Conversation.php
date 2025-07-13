<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $table = 'conversations';
    protected $fillable = [
        'receiver',
        'sender',

    ];

    public $timestamps = true;

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
