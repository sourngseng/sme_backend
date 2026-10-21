<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Allergen extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'allergens';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function menuVersions(): BelongsToMany
    {
        return $this->belongsToMany(MenuVersion::class, 'menu_allergens', 'allergen_id', 'menu_version_id')
            ->withPivot('severity', 'notes');
    }
}
