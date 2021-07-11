<?php

namespace Rabble\UserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Rabble\UserBundle\Entity\User;

class UserRepository extends ServiceEntityRepository
{
    public function save(User $user): void
    {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }
}
