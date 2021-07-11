<?php

namespace Rabble\UserBundle\Form\Choice;

use Rabble\UserBundle\Security\Task\TaskBundle;

class Task
{
    private TaskBundle $taskBundle;

    private string $taskName;

    public function __construct(TaskBundle $taskBundle, string $taskName)
    {
        $this->taskBundle = $taskBundle;
        $this->taskName = $taskName;
    }

    public function getTaskBundle(): TaskBundle
    {
        return $this->taskBundle;
    }

    public function getTaskName(): string
    {
        return $this->taskName;
    }
}
