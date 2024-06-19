<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ApiAuthController extends AbstractController
{
    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function login(): Response
    {
        // Le JWT est automatiquement créé par le guard, on retourne simplement l'utilisateur connecté
        $user = $this->getUser();
        return $this->json([
            'username' => $user->getUserIdentifier(),  // Modification ici
            'roles' => $user->getRoles(),
        ]);
    }
}