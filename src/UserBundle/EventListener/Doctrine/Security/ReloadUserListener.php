<?php

namespace Rabble\UserBundle\EventListener\Doctrine\Security;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Rabble\UserBundle\Entity\Role;
use Rabble\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class ReloadUserListener
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->handle($args->getEntity());
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->handle($args->getEntity());
    }

    private function reloadUser(): void
    {
        $currentToken = $this->tokenStorage->getToken();
        if (!$currentToken instanceof PostAuthenticationGuardToken || null === $currentToken->getUser()) {
            return;
        }
        $this->tokenStorage->setToken(new PostAuthenticationGuardToken($currentToken->getUser(), $currentToken->getProviderKey(), $currentToken->getUser()->getRoles()));
    }

    private function handle($entity): void
    {
        if ($entity instanceof User || $entity instanceof Role) {
            $this->reloadUser();
        }
    }
}
