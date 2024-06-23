<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController
{
    #[Route('/teacher/dashboard', name: 'teacher_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('teacher/dashboard.html.twig');
    }

    #[Route('/teacher/events', name: 'teacher_events')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(['status' => 'approved']);
        return $this->render('teacher/index.html.twig', ['events' => $events]);
    }

    #[Route('/teacher/events/create', name: 'teacher_create_event')]
    public function create(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier la disponibilité de la salle d'exposition pour les événements de type "exposition"
            if ($event->getType() === 'exposition') {
                /** @var \DateTime $startDate */
                $startDate = $event->getStartDate();
                /** @var \DateTime $endDate */
                $endDate = $event->getEndDate();
                /** @var \DateTime $bufferStart */
                $bufferStart = (clone $startDate)->modify('-15 days');
                /** @var \DateTime $bufferEnd */
                $bufferEnd = (clone $endDate)->modify('+15 days');

                $existingEvents = $eventRepository->createQueryBuilder('e')
                    ->where('e.status = :status')
                    ->andWhere('e.startDate < :bufferEnd')
                    ->andWhere('e.endDate > :bufferStart')
                    ->andWhere('e.type = :type')
                    ->setParameter('status', 'approved')
                    ->setParameter('bufferEnd', $bufferEnd)
                    ->setParameter('bufferStart', $bufferStart)
                    ->setParameter('type', 'exposition')
                    ->getQuery()
                    ->getResult();

                if (count($existingEvents) > 0) {
                    $this->addFlash('error', 'La salle d\'exposition n\'est pas disponible pour les dates demandées.');
                    return $this->render('teacher/create.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
            }

            $event->setStatus('pending');
            $event->setCreatedBy($this->getUser());
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('teacher_event_status');
        }

        return $this->render('teacher/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/teacher/events/availability', name: 'teacher_check_availability')]
    public function checkAvailability(Request $request, EventRepository $eventRepository): Response
    {
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        $events = [];
        $isAvailable = true;
        if ($startDate && $endDate) {
            $bufferStart = (new \DateTime($startDate))->modify('-15 days');
            $bufferEnd = (new \DateTime($endDate))->modify('+15 days');

            $events = $eventRepository->createQueryBuilder('e')
                ->where('e.status = :status')
                ->andWhere('e.startDate < :bufferEnd')
                ->andWhere('e.endDate > :bufferStart')
                ->andWhere('e.type = :type')
                ->setParameter('status', 'approved')
                ->setParameter('bufferEnd', $bufferEnd)
                ->setParameter('bufferStart', $bufferStart)
                ->setParameter('type', 'exposition')
                ->getQuery()
                ->getResult();

            $isAvailable = count($events) === 0;
        }

        return $this->render('teacher/availability.html.twig', [
            'events' => $events,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'isAvailable' => $isAvailable
        ]);
    }

    #[Route('/teacher/events/pending', name: 'teacher_pending_events')]
    public function pendingEvents(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(['status' => 'pending', 'createdBy' => $this->getUser()]);
        return $this->render('teacher/pending.html.twig', ['events' => $events]);
    }

    #[Route('/teacher/events/status', name: 'teacher_event_status')]
    public function eventStatus(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(['createdBy' => $this->getUser()]);
        return $this->render('teacher/status.html.twig', ['events' => $events]);
    }
}