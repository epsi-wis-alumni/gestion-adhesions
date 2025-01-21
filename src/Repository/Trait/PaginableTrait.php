<?php

namespace App\Repository\Trait;

use Doctrine\ORM\QueryBuilder;

trait PaginableTrait
{
    public function paginate(
        QueryBuilder $qb,
        int $page = 1,
        int $perPage = 50,
    ): QueryBuilder {
        return $qb->setMaxResults($perPage)
            ->setFirstResult(($page - 1) * $perPage);
    }
}
