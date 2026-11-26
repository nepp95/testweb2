<?php

namespace App\Models;

use App\Enums\SectionType;
use App\Models\Concerns\HasPosition;
use Database\Factories\SectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['type', 'title', 'anchor', 'nav_label', 'is_visible', 'content'])]
class Section extends Model
{
    /** @use HasFactory<SectionFactory> */
    use HasFactory, HasPosition;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => SectionType::class,
            'is_visible' => 'boolean',
            'content' => 'array',
        ];
    }

    /**
     * Get the items of the section, in their manual order.
     *
     * @return HasMany<SectionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(SectionItem::class)->orderBy('position');
    }

    /**
     * Get the items of one group.
     *
     * @return Collection<int, SectionItem>
     */
    public function itemsInGroup(string $group): Collection
    {
        return $this->items->where('group', $group)->values();
    }

    /**
     * Get a value from the type-specific content.
     */
    public function field(string $key): mixed
    {
        return data_get($this->content, $key);
    }

    /**
     * Get the CSS modifier for the chosen background.
     */
    public function backgroundClass(): string
    {
        return match ($this->field('background')) {
            'zand' => 'section-tint',
            default => '',
        };
    }

    /**
     * Scope the query to sections shown on the website.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function visible(Builder $query): void
    {
        $query->where('is_visible', true);
    }

    /**
     * Scope the query to the manual order.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('position');
    }

    /**
     * {@inheritDoc}
     */
    protected function positionSiblings(): Builder
    {
        return static::query();
    }
}
