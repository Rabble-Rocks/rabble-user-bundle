<?php

namespace Rabble\UserBundle\Event;

use Rabble\AdminBundle\Ui\Event\AbstractUiEvent;
use Rabble\UserBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class UserCreateEvent extends AbstractUiEvent
{
    private User $user;

    private Request $request;

    private FormInterface $form;

    public function __construct(User $user, FormInterface $form, Request $request, $panes = [])
    {
        parent::__construct($panes);
        $this->user = $user;
        $this->form = $form;
        $this->request = $request;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
