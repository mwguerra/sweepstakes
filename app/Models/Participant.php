<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
    ];

    public function sweepstakes(): BelongsTo
    {
        return $this->belongsTo(Sweepstakes::class);
    }
}
