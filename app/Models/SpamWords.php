<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpamWords extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $spam_words = [
        'spam_words', 'spamwords',
    ];
}
