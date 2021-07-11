<?php

namespace Rabble\UserBundle\UserActivity;

use Rabble\UserBundle\Entity\UserActivity;

interface UserActivityTypeInterface
{
    public function getName(): string;

    public function getTemplateName(UserActivity $activity): string;
}
