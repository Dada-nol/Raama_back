<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entries extends Model
{
    /**
     * The attributes that are mass assignable
     * @var list<string>
     */

    protected $fillable = [
        'souvenir_id',
        'user_id',
        'image_path',
        'caption',
        'posted_at'
    ];
}
