<?php

namespace Rabble\UserBundle\Security\Voter;

use Rabble\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserVoter implements VoterInterface
{
    /**
     * @param mixed $subject
     *
     * @return int
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        $user = $token->getUser();
        $attribute = $attributes[0];
        if (!$attribute instanceof User || !$user instanceof User) {
            return self::ACCESS_ABSTAIN;
        }
        if ($user->isSuperAdmin()) {
            return self::ACCESS_GRANTED;
        }
        if ($attribute->isSuperAdmin()) {
            return self::ACCESS_DENIED;
        }
        $userRoles = array_flip($user->getRoles());
        foreach ($attribute->getRoles() as $role) {
            if (!isset($userRoles[$role])) {
                return self::ACCESS_DENIED;
            }
        }

        return self::ACCESS_GRANTED;
    }
}
