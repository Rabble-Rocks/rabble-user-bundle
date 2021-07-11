<?php

namespace Rabble\UserBundle\EventListener\Doctrine\Security;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Rabble\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncoderListener
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
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
            $entity->setPassword($this->passwordEncoder->encodePassword($entity, $entity->getPassword()));
        }
    }
}
