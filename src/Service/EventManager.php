<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;
use DateTimeImmutable;

final class EventManager
{
    public function create(User $user, Event $event): void
    {
        $event
            ->setCreatedBy($user)
            ->setCreatedAt(new DateTimeImmutable())
        ;
    }

    public function update(User $user, Event $event): void
    {
        $event
            ->setUpdatedBy($user)
            ->setUpdatedAt(new DateTimeImmutable())
        ;
    }

    public function getStep(Event $event): int
    {
        $now = new DateTimeImmutable();

        return $event->getStartAt() > $now
            ? 1 : ($event->getStartAt() < $now && $event->getEndAt() > $now
            ? 2 : 3);
    }
}
