<?php

namespace App\Security;

use App\Entity\Teacher;
use App\Repository\TeacherRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TeacherUserProvider implements UserProviderInterface
{
    private $teacherRepository;

    public function __construct(TeacherRepository $teacherRepository)
    {
        $this->teacherRepository = $teacherRepository;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->teacherRepository->findOneByEmail($identifier);

        if (!$user) {
            throw new UserNotFoundException(sprintf('Teacher with email "%s" not found.', $identifier));
        }

        if (!$user->isApproved()) {
            throw new CustomUserMessageAuthenticationException('Your account is not approved yet.');
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return Teacher::class === $class;
    }

    // Deprecated method, should not be used in Symfony 5.3 and later
    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }
}