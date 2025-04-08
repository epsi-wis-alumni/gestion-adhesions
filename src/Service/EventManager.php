<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

final class EventManager
{
    public function __construct(
        private ContainerBagInterface $params,
        private SluggerInterface $slugger,
    ) { }
    public function create(User $user, Event $event): void
    {
        $event
            ->setCreatedBy($user)
        ;
    }

    public function update(User $user, Event $event): void
    {
        $event
            ->setUpdatedBy($user)
        ;
    }

    public function getStep(Event $event): int
    {
        $now = new \DateTimeImmutable();

        return $event->getStartAt() > $now
            ? 1 : ($event->getStartAt() < $now && $event->getEndAt() > $now
            ? 2 : 3);
    }

    public function setImage(
        $uploadedFile, 
        Event $event,
    ): void {
        $event_image_base_path = $this->params->get('event_image_base_path');

        if ($uploadedFile) {
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

            if (!is_dir($event_image_base_path)) {
                mkdir($event_image_base_path, 0777, true);
            }

            try {
                $uploadedFile->move($event_image_base_path, $newFilename);
            } catch (FileException $e) {
            }

            $event->setImageFileName($newFilename);
        }
    }
}
