<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Entry extends Model
{
    /** @use HasFactory<\Database\Factories\EntryFactory> */
    use HasFactory;

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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($entry) {
            if ($entry->image_path) {
                Storage::disk('public')->delete($entry->image_path);
            }
        });
    }
    /**
     * @return BelongsTo<User, Entry>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Souvenir, Entry>
     */
    public function souvenir(): BelongsTo
    {
        return $this->belongsTo(Souvenir::class);
    }
}
