<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Service\EventManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/event')]
final class EventController extends AbstractController
{
    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(
        EventRepository $eventRepository,
    ): Response {
        $onlyPublic = !$this->isGranted('IS_AUTHENTICATED');
        $pendingEvents = $eventRepository->findPending($onlyPublic);
        $inProgressEvents = $eventRepository->findInProgress($onlyPublic);
        $doneEvents = $eventRepository->findDone($onlyPublic);
        
        return $this->render('event/index.html.twig', [
            'pending_events' => $pendingEvents,
            'in_progress_events' => $inProgressEvents,
            'done_events' => $doneEvents,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
}
