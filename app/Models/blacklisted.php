<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class blacklisted extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $blacklisted = [
        'blacklisted_host',
    ];
	
}
