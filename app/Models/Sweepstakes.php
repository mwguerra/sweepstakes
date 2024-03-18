<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

class Sweepstakes extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'winner_email_message',
        'draw_time',
        'timezone',
        'is_over',
        'winner_id',
        'is_winner_notified',
    ];

    protected $appends = ['draw_time_raw', 'draw_time_utc'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'draw_time' => 'datetime',
            'is_over' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function winnerEmailFiles(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'winner_id');
    }

    protected function drawTimeRaw(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['draw_time'],
        );
    }

    protected function drawTimeUtc(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => Carbon::createFromFormat('Y-m-d H:i:s', $attributes['draw_time'], $attributes['timezone']),
        );
    }

    /**
     * Determine the winner of the sweepstakes.
     *
     * @return Participant|null
     */
    protected function determineWinner(): ?Participant
    {
        if ($this->participants->isEmpty()) {
            return null;
        }

        $winner = $this->participants()->inRandomOrder()->first();

        $this->winner()->associate($winner);

        return $winner;
    }
}
