<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Souvenir extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'cover_image',
        'is_closed'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'souvenir_users')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }
}
