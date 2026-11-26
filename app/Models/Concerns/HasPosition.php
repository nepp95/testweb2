<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Keeps a manual sort order in a "position" column, relative to the model's siblings.
 *
 * @mixin Model
 */
trait HasPosition
{
    /**
     * Get a query for the models that share an ordering with this model.
     *
     * @return Builder<static>
     */
    abstract protected function positionSiblings(): Builder;

    /**
     * Place new models at the end of their siblings.
     */
    public static function bootHasPosition(): void
    {
        static::creating(function (Model $model): void {
            /** @var static $model */
            if ($model->position === null) {
                $model->position = (int) $model->positionSiblings()->max('position') + 1;
            }
        });
    }

    /**
     * Swap places with the previous or next sibling.
     */
    public function move(string $direction): void
    {
        $isMovingUp = $direction === 'up';

        $neighbour = $this->positionSiblings()
            ->where('position', $isMovingUp ? '<' : '>', $this->position)
            ->orderBy('position', $isMovingUp ? 'desc' : 'asc')
            ->first();

        if ($neighbour === null) {
            return;
        }

        DB::transaction(function () use ($neighbour): void {
            [$this->position, $neighbour->position] = [$neighbour->position, $this->position];

            $this->save();
            $neighbour->save();
        });
    }
}
