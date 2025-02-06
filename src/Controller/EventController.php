<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/event')]
final class EventController extends AbstractController
{
    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        $pendingEvents = $eventRepository->findPending();
        $inProgressEvents = $eventRepository->findInProgress();
        $doneEvents = $eventRepository->findDone();

        return $this->render('event/index.html.twig', [
            'isClose' => false,
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
