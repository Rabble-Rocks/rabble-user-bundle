<?php

namespace Rabble\UserBundle\Menu;

use Rabble\AdminBundle\Menu\Event\ConfigureMenuEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ConfigureMenuListener
{
    private $checker;

    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getRootItem();
        if ($this->checker->isGranted('user.view')) {
            $menu->addChild('user', [
                'label' => 'menu.user',
                'route' => 'rabble_admin_user_index',
                'extras' => [
                    'translation_domain' => 'RabbleUserBundle',
                    'icon' => 'ti-user',
                    'icon_color' => 'orange',
                    'routes' => ['rabble_admin_user_view', 'rabble_admin_user_edit', 'rabble_admin_user_create'],
                ],
            ]);
        }
        if ($this->checker->isGranted('role.view')) {
            if (!($settings = $menu->getChild('settings'))) {
                $settings = $menu;
            } else {
                $settings->setDisplay(true);
                $settings->setExtra('routes', array_merge($settings->getExtra('routes', []), ['rabble_admin_role_index']));
            }
            $settings->addChild('role', [
                'label' => 'menu.role',
                'route' => 'rabble_admin_role_index',
                'extras' => [
                    'translation_domain' => 'RabbleUserBundle',
                    'routes' => ['rabble_admin_role_edit'],
                ],
            ]);
        }
    }
}
