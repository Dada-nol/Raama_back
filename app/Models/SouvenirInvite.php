<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SouvenirInvite extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var list<string>
     */
    protected $fillable = ['souvenir_id', 'token', 'expires_at'];

    /**
     * @return BelongsTo<Souvenir, Souvenir>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Souvenir::class, 'souvenir_id');
    }
}
