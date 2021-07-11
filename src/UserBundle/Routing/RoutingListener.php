<?php

namespace Rabble\UserBundle\Routing;

use Rabble\AdminBundle\Routing\Event\RoutingEvent;

class RoutingListener
{
    public function onRoutingLoad(RoutingEvent $event)
    {
        $event->addResources('xml', ['@RabbleUserBundle/Resources/config/routing.xml']);
    }
}
