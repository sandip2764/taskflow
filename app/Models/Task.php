<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
    ];
    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
        * Relationships
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
        * Scopes
    */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())
            ->where('status', '!=', 'completed');
    }

    public function scopeDueThisWeek($query)
    {
        return $query
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [
                Carbon::now()->startOfDay(),
                Carbon::now()->endOfWeek()
            ])
            ->where('status', '!=', 'completed');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
        });
    }

    public function scopeCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }
    
}
