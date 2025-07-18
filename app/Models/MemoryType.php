<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemoryType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'cover_image',
    ];
}
