<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SouvenirUser extends Model
{
    /** @use HasFactory<\Database\Factories\SouvenirUserFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable
     * @var list<string>
     */

    protected $fillable = [
        'souvenir_id',
        'user_id',
        'pseudo',
        'role',
        'joined_at',
        'last_visited_at',
    ];

    /**
     * @return BelongsTo<User, SouvenirUser>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Souvenir, SouvenirUser>
     */
    public function souvenir(): BelongsTo
    {
        return $this->belongsTo(Souvenir::class);
    }
}
