<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * Relationships
    */
    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }
}
