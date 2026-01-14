<?php

namespace App\Relations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * A no-op relation that never executes a database query.
 * Useful when a relationship should resolve to null on certain connections.
 */
class NullRelation extends Relation
{
    public function __construct(Model $parent)
    {
        parent::__construct($parent->newQuery(), $parent);
    }

    public function addConstraints(): void
    {
        // No constraints needed.
    }

    public function addEagerConstraints(array $models): void
    {
        // Prevent eager constraints from running.
    }

    public function initRelation(array $models, $relation): array
    {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }

        return $models;
    }

    public function match(array $models, Collection $results, $relation): array
    {
        // Relations are already initialized to null.
        return $models;
    }

    public function getResults()
    {
        return null;
    }

    public function getEager(): Collection
    {
        return new Collection();
    }
}

