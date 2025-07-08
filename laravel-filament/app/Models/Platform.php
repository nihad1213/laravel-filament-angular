<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Platform extends Model
{
    protected $fillable = [
        'name'
    ];

    // Games Belongs to Platforms
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class);
    }

}
