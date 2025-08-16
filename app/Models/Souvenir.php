<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Souvenir extends Model
{
    /** @use HasFactory<\Database\Factories\SouvenirFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'memory_type_id',
        'title',
        'cover_image',
        'memory_points',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($souvenir) {
            // Supprimer l'image de couverture du souvenir
            if ($souvenir->cover_image) {
                Storage::disk('public')->delete($souvenir->cover_image);
            }

            // Supprimer toutes les entries liées et leurs images
            foreach ($souvenir->entries as $entry) {
                if ($entry->image_path) {
                    Storage::disk('public')->delete($entry->image_path);
                }
                $entry->delete();
            }
        });
    }

    /**
     * @return BelongsToMany<User, Souvenir>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'souvenir_users')
            ->withPivot('pseudo', 'role', 'joined_at', 'last_visited_at')
            ->withTimestamps();
    }

    /**
     * @return BelongsTo<User, Souvenir>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public function invites()
    {
        return $this->hasMany(SouvenirInvite::class);
    }
}
