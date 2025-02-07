<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use DateTimeImmutable;

final class EventManager
{
    public function __construct(
        private EventRepository $eventRepository,
    ) {}

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

    public function getPublic(array $events): array
{
    $publicEvents = $this->eventRepository->findByPublic();

    return array_filter($events, function ($event) use ($publicEvents) {
        return in_array($event, $publicEvents, true);
    });
}
}
