<?php

namespace App\Repository\Trait;

use Doctrine\ORM\QueryBuilder;

trait SearchableTrait
{
    public function search(QueryBuilder $qb, string $needle, array $haystack): QueryBuilder
    {
        $alias = $qb->getRootAliases()[0];

        foreach ($haystack as $field) {
            $paramName = $field . '_param';

            $qb->orWhere("$alias.$field LIKE :$paramName")
                ->setParameter($paramName, '%' . $needle . '%');
        }

        return $qb;
    }
}
