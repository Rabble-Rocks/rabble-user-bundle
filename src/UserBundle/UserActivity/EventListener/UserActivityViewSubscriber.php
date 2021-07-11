<?php

namespace Rabble\UserBundle\UserActivity\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Rabble\AdminBundle\Ui\Panel\ContentPanel;
use Rabble\AdminBundle\Ui\Panel\Tab;
use Rabble\AdminBundle\Ui\Panel\TabbedPanel;
use Rabble\UserBundle\Entity\UserActivity;
use Rabble\UserBundle\Event\UserViewEvent;
use Rabble\UserBundle\UserActivity\UserActivityTypeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class UserActivityViewSubscriber implements EventSubscriberInterface
{
    private Environment $twig;
    /** @var ArrayCollection|UserActivityTypeInterface[] */
    private $activityTypes;

    private EntityManagerInterface $entityManager;

    private TranslatorInterface $translator;

    public function __construct(
        Environment $twig,
        ArrayCollection $activityTypes,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ) {
        $this->twig = $twig;
        $this->activityTypes = $activityTypes;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserViewEvent::class => 'onUserView',
        ];
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function onUserView(UserViewEvent $event)
    {
        $tabbedPanel = new TabbedPanel([
            'tabs' => [],
            'name' => 'user-view',
        ]);
        $event->addRightPane($tabbedPanel, 'panel-right-tabbed');
        $event->addLeftPane(new ContentPanel([
            'no_container' => true,
            'content' => $this->twig->render('@RabbleUser/User/view/user_info.html.twig', [
                'user' => $event->getUser(),
                'actionRow' => $event->getActionRow(),
            ]),
        ]), 'panel-left-user-info');
        $items = [];
        $activities = $this->entityManager->getRepository(UserActivity::class)->findBy(['user' => $event->getUser()], ['createdAt' => 'DESC'], 25);
        foreach ($activities as $activity) {
            if (!isset($this->activityTypes[$activity->getType()])) {
                continue;
            }
            $activityType = $this->activityTypes[$activity->getType()];
            $items[] = [
                'template' => $activityType->getTemplateName($activity),
                'context' => ['activity' => $activity],
            ];
        }
        $tabbedPanel->addTab(new Tab(
            [
                'label' => $this->translator->trans('user.recent_activity', [], 'RabbleUserBundle'),
                'content' => $this->twig->render('@RabbleUser/User/view/recent_activity.html.twig', [
                    'items' => $items,
                ]),
            ]
        ));
    }
}
