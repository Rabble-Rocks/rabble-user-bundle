<?php

namespace Rabble\UserBundle\Event;

use Rabble\AdminBundle\Ui\Section\ActionRow;
use Rabble\AdminBundle\Ui\UiInterface;
use Rabble\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class UserViewEvent extends Event
{
    private const DEFAULT_NAME = 'default';

    private Request $request;

    private User $user;
    /** @var ActionRow[] */
    private array $actionRow = [];
    /** @var UiInterface[][] */
    private array $leftPanes = [];
    /** @var UiInterface[][] */
    private array $rightPanes = [];

    public function __construct(User $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function addAction(UiInterface $action, string $rowName = self::DEFAULT_NAME): void
    {
        if (!isset($this->actionRow[$rowName])) {
            $this->actionRow[$rowName] = new ActionRow();
        }
        $this->actionRow[$rowName]->addItem($action);
    }

    public function addLeftPane(UiInterface $pane, string $name = self::DEFAULT_NAME): void
    {
        if (!isset($this->leftPanes[$name])) {
            $this->leftPanes[$name] = [];
        }
        $this->leftPanes[$name][] = $pane;
    }

    public function addRightPane(UiInterface $pane, string $name = self::DEFAULT_NAME): void
    {
        if (!isset($this->rightPanes[$name])) {
            $this->rightPanes[$name] = [];
        }
        $this->rightPanes[$name][] = $pane;
    }

    /**
     * @return ActionRow[]
     */
    public function getActionRow(): array
    {
        return $this->actionRow;
    }

    /**
     * @return UiInterface[][]
     */
    public function getLeftPanes(): array
    {
        return $this->leftPanes;
    }

    /**
     * @return UiInterface[][]
     */
    public function getRightPanes(): array
    {
        return $this->rightPanes;
    }

    public function clear(): void
    {
        $this->actionRow = [];
        $this->leftPanes = [];
        $this->rightPanes = [];
    }
}
