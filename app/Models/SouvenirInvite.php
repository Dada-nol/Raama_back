<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SouvenirInvite extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     * @var list<string>
     */
    protected $fillable = ['souvenir_id', 'token', 'expires_at'];

    /**
     * @return BelongsTo<Souvenir, Souvenir>
     */
    public function souvenir(): BelongsTo
    {
        return $this->belongsTo(Souvenir::class, 'souvenir_id');
    }
}
