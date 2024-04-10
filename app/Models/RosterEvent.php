<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RosterEvent extends Model
{
    protected $fillable = [
        'type',
        'location',
        'date',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'date' => 'date',
    ];

    public function scopeBetween(Builder $builder, ?int $from, ?int $to): Builder
    {
        if ($from && $to) {
            return $builder->whereBetween('date', [
                Carbon::createFromTimestamp($from),
                Carbon::createFromTimestamp($to)
            ]);
        }

        return $builder;
    }

    public function scopeOfType(Builder $builder, ?string $type): Builder
    {
        if ($type !== null) {
            return $builder->where('type', $type);
        }

        return $builder;
    }

    public function scopeForNextWeek(Builder $builder, ?bool $requested): Builder
    {
        if ($requested) {
            return $builder->whereBetween('date', [
                Carbon::now(),
                Carbon::now()->addWeek()
            ]);
        }

        return $builder;
    }

    public function scopeForLocation(Builder $builder, ?string $location): Builder
    {
        if ($location) {
            return $builder->where('location', $location);
        }

        return $builder;
    }
}
