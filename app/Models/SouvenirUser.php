<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SouvenirUser extends Model
{
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
        'can_edit'
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
