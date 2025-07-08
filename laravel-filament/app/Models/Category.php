<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'name'
    ];

    // Games Belogns to Categories    
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class);
    }
}
