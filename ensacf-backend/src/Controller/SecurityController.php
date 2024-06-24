<?php

// src/Controller/SecurityController.php

namespace App\Controller;

use App\Entity\Teacher;
use App\Form\TeacherRegistrationFormType;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $this->logger->info('Attempting admin login.');

        if ($this->isGranted('ROLE_ADMIN')) {
            $this->logger->info('Admin already logged in, redirecting to admin dashboard.');
            return $this->redirectToRoute('app_event_crud_index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            $this->logger->error('Admin login error: ' . $error->getMessage());
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        $this->logger->info('Admin logged out.');
    }

    #[Route('/teacher/login', name: 'teacher_login')]
    public function teacherLogin(AuthenticationUtils $authenticationUtils): Response
    {
        $this->logger->info('Attempting teacher login.');

        if ($this->isGranted('ROLE_TEACHER')) {
            $this->logger->info('Teacher already logged in, redirecting to teacher dashboard.');
            return $this->redirectToRoute('teacher_dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            $this->logger->error('Teacher login error: ' . $error->getMessage());
        }

        return $this->render('security/teacher_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/teacher/logout', name: 'teacher_logout')]
    public function teacherLogout(): void
    {
        $this->logger->info('Teacher logged out.');
    }

    #[Route('/teacher/register', name: 'teacher_register')]
    public function registerTeacher(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $teacher = new Teacher();
        $form = $this->createForm(TeacherRegistrationFormType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $validator->validate($teacher);
            if (count($errors) > 0) {
                $this->addFlash('error', (string) $errors);
            } else {
                $hashedPassword = $passwordHasher->hashPassword($teacher, $teacher->getPassword());
                $teacher->setPassword($hashedPassword);
                $teacher->setRoles(['ROLE_TEACHER']);
                $teacher->setApproved(false); // Set approved to false by default

                $entityManager->persist($teacher);
                $entityManager->flush();

                $this->addFlash('success', 'Votre compte a été créé et est en attente d\'approbation.');

                return $this->redirectToRoute('teacher_login');
            }
        }

        return $this->render('security/teacher_register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/approve_teacher/{id}', name: 'admin_approve_teacher')]
    public function approveTeacher(Teacher $teacher, EntityManagerInterface $entityManager): Response
    {
        $teacher->setApproved(true);
        $entityManager->flush();

        $this->addFlash('success', 'Teacher account has been approved.');

        return $this->redirectToRoute('app_event_crud_index');
    }
}