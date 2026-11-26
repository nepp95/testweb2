<?php

namespace App\Models;

use App\Models\Concerns\HasPosition;
use Database\Factories\SectionItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['group', 'title', 'content'])]
class SectionItem extends Model
{
    /** @use HasFactory<SectionItemFactory> */
    use HasFactory, HasPosition;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    /**
     * Get the section the item belongs to.
     *
     * @return BelongsTo<Section, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get a value from the type-specific content.
     */
    public function field(string $key): mixed
    {
        return data_get($this->content, $key);
    }

    /**
     * {@inheritDoc}
     */
    protected function positionSiblings(): Builder
    {
        return static::query()
            ->where('section_id', $this->section_id)
            ->where('group', $this->group);
    }
}
