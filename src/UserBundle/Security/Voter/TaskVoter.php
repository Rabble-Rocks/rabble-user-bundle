<?php

namespace Rabble\UserBundle\Security\Voter;

use Rabble\UserBundle\Entity\User;
use Rabble\UserBundle\Security\Task\TaskCollection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TaskVoter implements VoterInterface
{
    private $taskCollection;

    public function __construct(TaskCollection $taskCollection)
    {
        $this->taskCollection = $taskCollection;
    }

    /**
     * @param mixed $subject
     *
     * @return int
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        $attribute = $attributes[0];
        $user = $token->getUser();
        if (!is_string($attribute) || !$user instanceof User || !$this->taskExists($attribute)) {
            return self::ACCESS_ABSTAIN;
        }
        if ($user->isSuperAdmin()) {
            return self::ACCESS_GRANTED;
        }
        foreach ($user->getRoleHierarchy() as $role) {
            if (in_array($attribute, $role->getTasks(), true)) {
                return self::ACCESS_GRANTED;
            }
        }

        return self::ACCESS_ABSTAIN;
    }

    private function taskExists($taskName)
    {
        $bundleName = substr($taskName, 0, strpos($taskName, '.'));
        $taskName = substr($taskName, strlen($bundleName) + 1);

        return $this->taskCollection->containsKey($bundleName) && $this->taskCollection->get($bundleName)->hasTask($taskName);
    }
}
