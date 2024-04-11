<?php

namespace Utyemma\LaraNotice\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mailable extends Model {
    use HasFactory;

    protected $fillable = ['content', 'subject', 'sent', 'mailable'];

    protected $attributes = [
        'sent' => 0
    ];

}
