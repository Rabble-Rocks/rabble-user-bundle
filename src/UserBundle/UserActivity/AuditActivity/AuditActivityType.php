<?php

namespace Rabble\UserBundle\UserActivity\AuditActivity;

use Rabble\UserBundle\Entity\UserActivity;
use Rabble\UserBundle\UserActivity\UserActivityTypeInterface;

class AuditActivityType implements UserActivityTypeInterface
{
    public const TYPE_NAME = 'audit';

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    public function getTemplateName(UserActivity $activity): string
    {
        $payload = $activity->getPayload();

        return $payload['templateName'] ?? '@RabbleUser/UserActivity/audit.html.twig';
    }
}
