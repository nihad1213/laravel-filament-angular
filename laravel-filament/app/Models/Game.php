<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    protected $fillable = [
        'name',
        'description',
        'cover_image',
        'release_date'
    ];

    // Categories are belongs to Game
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    // Platforms are belongs to Game
    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class);
    }

}
