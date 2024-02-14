<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    public $resourceType= 'articles';   // Introducido por tema 26

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
//    protected $primaryKey = 'slug';
//    protected $keyType = 'string';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeYear(Builder $query, $year): void
    {
        $query->whereYear('created_at', $year);
    }

    public function scopeMonth(Builder $query, $month): void
    {
        $query->whereMonth('created_at', $month);
    }
}
