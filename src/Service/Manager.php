<?php

namespace App\Service;

use App\Entity\Election;
use App\Entity\Event;

final class Manager {
    public function __construct(
        private ElectionManager $electionManager,
        private EventManager $eventManager,
    ) { }
    public function orderByStep(array $entities): array
    
    {
        $futur = [];
        $present = [];
        $past = [];

        foreach ($entities as $entity) {
            $step = match($entity::class) {
                Election::class => $this->electionManager->getStep($entity),
                Event::class => $this->eventManager->getStep($entity),
            };

            switch ($step) {
                case 1:
                    $futur[] = $entity;
                    break;
                case 2:
                    $present[] = $entity;
                    break;
                case 3:
                    $past[] = $entity;
                    break;
            }
        }
        return array_merge($futur, $present, $past);
    }
}
