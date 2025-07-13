<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $table = 'conversations';
    protected $fillable = [
        'receiver',
        'sender',
        'status',
        'is_open',
    ];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    public $timestamps = true;

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
