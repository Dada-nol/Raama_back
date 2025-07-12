<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * @return BelongsToMany<User, Souvenir>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'souvenir_users')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * @return HasMany<Entry, Souvenir>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }
}
