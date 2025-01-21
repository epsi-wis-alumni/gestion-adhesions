<?php

namespace App\Repository\Trait;

use Doctrine\ORM\QueryBuilder;

trait OrderableTrait
{
    public function orderBy(QueryBuilder $qb, array $orderBy): QueryBuilder
    {
        $alias = $qb->getRootAliases()[0];

        foreach ($orderBy as $sort => $order) {
            $qb->orderBy("$alias.$sort", $order);
        }

        return $qb;
    }
}
