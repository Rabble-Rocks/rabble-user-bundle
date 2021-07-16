<?php

namespace Rabble\UserBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Rabble\UserBundle\Entity\User;

/**
 * @method User[]    findAll()
 * @method null|User find($id)
 */
class UserRepository extends ServiceEntityRepository
{
    public function save(User $user): void
    {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }
}
