<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Teacher;
use App\Form\AdminType;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{
    #[Route('/admin/approve/teacher/{id}', name: 'admin_approve_teacher')]
    public function approveTeacher(Teacher $teacher, EntityManagerInterface $entityManager): Response
    {
        $teacher->setApproved(true);
        $entityManager->flush();

        $this->addFlash('success', 'Teacher account has been approved.');

        return $this->redirectToRoute('admin_pending_teachers');
    }

    #[Route('/admin/pending/teachers', name: 'admin_pending_teachers')]
    public function pendingTeachers(TeacherRepository $teacherRepository): Response
    {
        $pendingTeachers = $teacherRepository->findBy(['approved' => false]);

        return $this->render('admin/pending_teachers.html.twig', ['teachers' => $pendingTeachers]);
    }

    #[Route('/admin/create', name: 'admin_create_admin')]
    public function createAdmin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $admin = new Admin();
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordHasher->hashPassword($admin, $admin->getPassword());
            $admin->setPassword($password);

            $entityManager->persist($admin);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_crud_index');
        }

        return $this->render('admin/create_admin.html.twig', ['form' => $form->createView()]);
    }
}