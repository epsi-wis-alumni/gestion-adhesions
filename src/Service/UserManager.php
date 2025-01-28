<?php

namespace App\Service;

use App\Entity\Settings;
use App\Entity\User;
use App\Enum\DefaultSettings;
use DateTimeImmutable;
use LogicException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class UserManager
{
    public function delete(User $by, User $who, DateTimeImmutable $when = new DateTimeImmutable()): void
    {
        $who
            ->setDeletedBy($by)
            ->setDeletedAt($when)
        ;
    }

    public function approve(User $who, User $by): void
    {
        $who
            ->setRejectedBy(null)
            ->setRejectedAt(null)
            ->setApprovedBy($by)
            ->setApprovedAt(new DateTimeImmutable())
        ;
    }

    public function reject(User $who, User $by): void
    {
        $who
            ->setApprovedBy(null)
            ->setApprovedAt(null)
            ->setRejectedBy($by)
            ->setRejectedAt(new DateTimeImmutable())
        ;
    }

    public function addRole(User $to, string $role): void
    {
        if (!str_starts_with($role, 'ROLE_')) {
            throw new LogicException('A role must start with "ROLE_", but "' . $role . '" given.');
        }
        $roles = $to->getRoles();
        $roles[] = $role;
        $to->setRoles($roles);
    }

    public function removeRole(User $to, string $role): void
    {
        $to->setRoles(array_diff($to->getRoles(), [$role]));
    }
}
