<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function souvenir()
    {
        return $this->belongsTo(Souvenir::class);
    }
}
