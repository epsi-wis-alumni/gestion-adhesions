<?php

namespace App\Repository\Trait;

use Doctrine\ORM\QueryBuilder;

trait SearchableTrait
{
    public function search(QueryBuilder $qb, string $needle, array $haystack): QueryBuilder
    {
        foreach ($haystack as $field) {
            $alias = $qb->getRootAliases()[0];
            $paramName = str_replace('.', '_', $field).'_param';

            // Gestion des champs imbriqués (relation.user.firstname)
            if (str_contains((string) $field, '.')) {
                [$relationAlias, $relationField] = explode('.', (string) $field);
                $qb->orWhere("$relationAlias.$relationField LIKE :$paramName")
                    ->setParameter($paramName, "%$needle%");
            } else {
                $qb->orWhere("$alias.$field LIKE :$paramName")
                    ->setParameter($paramName, "%$needle%");
            }
        }

        return $qb;
    }
}
