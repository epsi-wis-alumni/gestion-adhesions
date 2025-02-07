<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Service\EventManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/event')]
final class EventController extends AbstractController
{
    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(
        EventRepository $eventRepository,
        EventManager $eventManager,
    ): Response {
        $pendingEvents = $eventRepository->findPending();
        $publicPendingEvents = $eventManager->getPublic($pendingEvents);
        $inProgressEvents = $eventRepository->findInProgress();
        $publicInProgressEvents = $eventManager->getPublic($inProgressEvents);
        $doneEvents = $eventRepository->findDone();
        $publicDoneEvents = $eventManager->getPublic($doneEvents);
        
        return $this->render('event/index.html.twig', [
            'pending_events' => $pendingEvents,
            'public_pending_events' => $publicPendingEvents,
            'in_progress_events' => $inProgressEvents,
            'public_in_progress_events' => $publicInProgressEvents,
            'done_events' => $doneEvents,
            'public_done_events' => $publicDoneEvents,
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
