<?php

// src/Controller/EventController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EventRepository;
use App\Entity\Event;

class EventController extends AbstractController
{
    #[Route('/events', name: 'public_events', methods: ['GET'])]
    public function list(EventRepository $eventRepository, Request $request): Response
    {
        $sortBy = $request->query->get('sort', 'date');  // Par défaut, trier par date

        switch ($sortBy) {
            case 'type':
                $events = $eventRepository->findByType();
                break;
            case 'title':
                $events = $eventRepository->findByTitle();
                break;
            case 'location':
                $events = $eventRepository->findByLocation();
                break;
            case 'date':
            default:
                $events = $eventRepository->findByDate();
                break;
        }

        return $this->render('event/index.html.twig', ['events' => $events]);
    }

    #[Route('/events/filter', name: 'filter_events', methods: ['GET'])]
    public function filterEvents(Request $request, EventRepository $eventRepository): Response
    {
        $start = new \DateTime($request->query->get('start'));
        $end = new \DateTime($request->query->get('end'));

        $events = $eventRepository->findEventsBetweenDates($start, $end);

        return $this->render('event/index.html.twig', ['events' => $events]);
    }

    #[Route('/events/{id}', name: 'public_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
}