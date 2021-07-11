<?php

namespace Rabble\UserBundle\Security\Task;

class TaskBundle
{
    private string $name;

    private array $tasks;

    public function __construct(string $name, array $tasks)
    {
        $this->name = $name;
        $this->tasks = array_combine($tasks, $tasks);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function setTasks(array $tasks): void
    {
        $this->tasks = array_combine($tasks, $tasks);
    }

    public function addTask(string $task): void
    {
        $this->tasks[$task] = $task;
    }

    public function addTasks(array $tasks): void
    {
        foreach ($tasks as $task) {
            $this->addTask($task);
        }
    }

    public function hasTask($taskName): bool
    {
        return isset($this->tasks[$taskName]);
    }
}
