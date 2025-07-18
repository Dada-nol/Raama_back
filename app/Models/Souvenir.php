<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Souvenir extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var list<string>
     */
    protected $fillable = [
        'memory_type_id',
        'title',
        'description',
        'cover_image',
        'memory_points',
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

    /**
     * @return BelongsTo<MemoryType, Souvenir>
     */
    public function memoryType(): BelongsTo
    {
        return $this->belongsTo(MemoryType::class);
    }
}
