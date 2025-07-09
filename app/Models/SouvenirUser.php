<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SouvenirUser extends Model
{
    /**
     * The attributes that are mass assignable
     * @var list<string>
     */

    protected $fillable = [
        'souvenir_id',
        'user_id',
        'role',
        'joined_at'
    ];
}
