<?php

namespace Rabble\UserBundle\EventListener\Doctrine\Security;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Rabble\UserBundle\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordEncoderListener
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->handle($args);
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->handle($args);
    }

    private function handle(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            if ($args instanceof PreUpdateEventArgs && !$args->hasChangedField('password')) {
                return;
            }
            $entity->setPassword($this->passwordHasher->hashPassword($entity, $entity->getPassword()));
        }
    }
}
