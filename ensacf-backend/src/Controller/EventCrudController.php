<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Admin;
use App\Entity\Teacher;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/event/crud')]
class EventCrudController extends AbstractController
{
    #[Route('/', name: 'app_event_crud_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        $approvedEvents = $eventRepository->findBy(['status' => 'approved']);
        return $this->render('event_crud/index.html.twig', [
            'events' => $approvedEvents,
        ]);
    }

    #[Route('/new', name: 'app_event_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photo')->getData();
            if ($file) {
                $filename = $this->generateUniqueFileName().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('photos_directory'),
                        $filename
                    );
                    $event->setPhoto($filename);
                } catch (FileException $e) {
                    // Handle exception
                }
            }

            $user = $this->getUser();
            if ($user instanceof Admin) {
                $event->setStatus('approved');
                $event->setCreatedByAdmin($user);
            } elseif ($user instanceof Teacher) {
                $event->setStatus('pending');
                $event->setCreatedBy($user);
            } else {
                throw new \LogicException('Unknown user type.');
            }

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_crud_index');
        }

        return $this->renderForm('event_crud/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_crud_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event_crud/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photo')->getData();
            if ($file) {
                $filename = $this->generateUniqueFileName().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('photos_directory'),
                        $filename
                    );
                    $event->setPhoto($filename);
                } catch (FileException $e) {
                    // Handle exception
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_event_crud_index');
        }

        return $this->renderForm('event_crud/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_crud_index');
    }

    #[Route('/pending', name: 'app_event_crud_pending', methods: ['GET'])]
    public function pending(EventRepository $eventRepository): Response
    {
        $pendingEvents = $eventRepository->findBy(['status' => 'pending']);

        return $this->render('event_crud/pending.html.twig', ['events' => $pendingEvents]);
    }

    #[Route('/approve/{id}', name: 'app_event_crud_approve', methods: ['POST'])]
    public function approve(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('approve'.$event->getId(), $request->request->get('_token'))) {
            $event->setStatus('approved');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_crud_pending');
    }

    #[Route('/reject/{id}', name: 'app_event_crud_reject', methods: ['POST'])]
    public function reject(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('reject'.$event->getId(), $request->request->get('_token'))) {
            $event->setStatus('rejected');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_crud_pending');
    }

    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}