<?php

namespace Rabble\UserBundle\Controller;

use Rabble\AdminBundle\Ui\Panel\ContentPanel;
use Rabble\AdminBundle\Ui\Panel\Tab;
use Rabble\AdminBundle\Ui\Panel\TabbedPanel;
use Rabble\UserBundle\Entity\User;
use Rabble\UserBundle\Event\UserCreateEvent;
use Rabble\UserBundle\Event\UserEditEvent;
use Rabble\UserBundle\Event\UserFormEvent;
use Rabble\UserBundle\Event\UserViewEvent;
use Rabble\UserBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    private EventDispatcherInterface $eventDispatcher;

    private TranslatorInterface $translator;

    public function __construct(EventDispatcherInterface $eventDispatcher, TranslatorInterface $translator)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
    }

    /**
     * @Security("is_granted('user.view')")
     */
    public function indexAction(): Response
    {
        return $this->render('@RabbleUser/User/index.html.twig');
    }

    /**
     * @ParamConverter("user", converter="implemented_entity", options={"entity" = "User"})
     * @Security("is_granted('user.view')")
     */
    public function viewAction(Request $request, User $user): Response
    {
        /** @var UserViewEvent $event */
        $event = $this->eventDispatcher->dispatch(new UserViewEvent($user, $request));

        return $this->render('@RabbleUser/User/view.html.twig', [
            'user' => $user,
            'actionRow' => $event->getActionRow(),
            'leftPanes' => $event->getLeftPanes(),
            'rightPanes' => $event->getRightPanes(),
        ]);
    }

    /**
     * @Security("is_granted('user.create')")
     */
    public function createAction(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $userClass = $em->getRepository('User')->getClassName();
        /** @var User $user */
        $user = new $userClass();
        $form = $this->createForm(UserType::class, $user)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'The user has been saved.');
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('rabble_admin_user_index');
        }
        $formView = $form->createView();
        /** @var UserFormEvent $formEvent */
        $formEvent = $this->eventDispatcher->dispatch(new UserFormEvent(
            $user,
            $form,
            $request,
            new TabbedPanel([
                'tabs' => [new Tab([
                    'label' => $this->translator->trans('menu.user.create', [], 'RabbleUserBundle'),
                    'content' => $this->renderView('@RabbleUser/User/form.html.twig', [
                        'form' => $formView,
                        'heading' => $this->translator->trans('menu.user.create', [], 'RabbleUserBundle'),
                    ]),
                ])],
            ])
        ));
        /** @var UserCreateEvent $event */
        $event = $this->eventDispatcher->dispatch(new UserCreateEvent($user, $form, $request, [
            'default' => [
                new ContentPanel([
                    'no_container' => true,
                    'content' => $this->renderView('@RabbleUser/User/create/form.html.twig', [
                        'form' => $formView,
                        'container' => $formEvent->getPane(),
                    ]),
                ]),
            ],
        ]));

        return $this->render('@RabbleUser/User/create.html.twig', [
            'panes' => $event->getPanesFlattened(),
        ]);
    }

    /**
     * @ParamConverter("user", converter="implemented_entity", options={"entity" = "User"})
     * @Security("(is_granted(request.attributes.get('user')) || is_granted('role.overrule')) && is_granted('user.edit')")
     */
    public function editAction(Request $request, User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(UserType::class, $user)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'The user has been saved.');
            $em->flush();
            $em->refresh($user);
            $form = $this->createForm(UserType::class, $user);
        }
        $formView = $form->createView();
        /** @var UserFormEvent $formEvent */
        $formEvent = $this->eventDispatcher->dispatch(new UserFormEvent(
            $user,
            $form,
            $request,
            new TabbedPanel([
                'tabs' => [new Tab([
                    'label' => $this->translator->trans('menu.user.edit', [], 'RabbleUserBundle'),
                    'content' => $this->renderView('@RabbleUser/User/form.html.twig', [
                        'form' => $formView,
                        'user' => $user,
                        'heading' => $this->translator->trans('menu.user.edit', [], 'RabbleUserBundle'),
                    ]),
                ])],
            ])
        ));
        /** @var UserCreateEvent $event */
        $event = $this->eventDispatcher->dispatch(new UserEditEvent($user, $form, $request, [
            'default' => [
                new ContentPanel([
                    'no_container' => true,
                    'content' => $this->renderView('@RabbleUser/User/edit/form.html.twig', [
                        'form' => $formView,
                        'user' => $user,
                        'container' => $formEvent->getPane(),
                    ]),
                ]),
            ],
        ]));

        return $this->render('@RabbleUser/User/edit.html.twig', [
            'panes' => $event->getPanesFlattened(),
        ]);
    }

    /**
     * @ParamConverter("user", converter="implemented_entity", options={"entity" = "User"})
     * @Security("(is_granted(request.attributes.get('user')) || is_granted('role.overrule')) && is_granted('user.delete')")
     */
    public function deleteAction(User $user): Response
    {
        if ($user === $this->getUser()) {
            return $this->redirectToRoute('rabble_admin_user_index');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('rabble_admin_user_index');
    }
}
