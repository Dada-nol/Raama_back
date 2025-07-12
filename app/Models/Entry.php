<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
