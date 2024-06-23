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
    private $typeColors = [
        'exposition' => '#ff9f89',
        'conference' => '#1e7cb4',
        'table_ronde' => '#a3b0fb',
        'seminaire' => '#78c4d4',
        'residence' => '#d3a3fa',
        'vie_etudiante' => '#ffc107',
        'evenements_externes' => '#6c757d',
        'autres' => '#28a745',
        'vie_ecole' => '#d3d3d3'
    ];

    #[Route('/events/calendar', name: 'events_calendar', methods: ['GET'], priority: 10)]
    public function calendar(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(['status' => 'approved']);

        $eventsArray = [];
        foreach ($events as $event) {
            $eventsArray[] = [
                'title' => $event->getTitle(),
                'start' => $event->getStartDate()->format('Y-m-d\TH:i:s'),
                'end' => $event->getEndDate()->format('Y-m-d\TH:i:s'),
                'color' => $this->typeColors[$event->getType()] ?? '#d3d3d3',
                'url' => $this->generateUrl('public_event_show', ['id' => $event->getId()]),
            ];
        }

        return $this->render('event/calendar.html.twig', [
            'events' => json_encode($eventsArray),
        ]);
    }

    #[Route('/events', name: 'public_events', methods: ['GET'])]
    public function list(EventRepository $eventRepository, Request $request): Response
    {
        $sortBy = $request->query->get('sort', 'date');  // Par défaut, trier par date

        switch ($sortBy) {
            case 'type':
                $events = $eventRepository->findByType('approved');
                break;
            case 'title':
                $events = $eventRepository->findByTitle('approved');
                break;
            case 'location':
                $events = $eventRepository->findByLocation('approved');
                break;
            case 'date':
            default:
                $events = $eventRepository->findByDate('approved');
                break;
        }

        return $this->render('event/index.html.twig', ['events' => $events]);
    }

    #[Route('/events/filter', name: 'filter_events', methods: ['GET'])]
    public function filterEvents(Request $request, EventRepository $eventRepository): Response
    {
        $start = new \DateTime($request->query->get('start'));
        $end = new \DateTime($request->query->get('end'));

        $events = $eventRepository->findApprovedEventsBetweenDates($start, $end);

        return $this->render('event/index.html.twig', ['events' => $events]);
    }

    #[Route('/events/{id}', name: 'public_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        // Vérifiez si l'événement est approuvé avant de l'afficher
        if ($event->getStatus() !== 'approved') {
            throw $this->createNotFoundException('L\'événement demandé n\'existe pas.');
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
}