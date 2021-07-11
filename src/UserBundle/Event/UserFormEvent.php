<?php

namespace Rabble\UserBundle\Event;

use Rabble\AdminBundle\Ui\Event\AbstractSimpleUiEvent;
use Rabble\UserBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class UserFormEvent extends AbstractSimpleUiEvent
{
    private User $user;

    private Request $request;

    private FormInterface $form;

    public function __construct(User $user, FormInterface $form, Request $request, $pane = null)
    {
        parent::__construct($pane);
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
