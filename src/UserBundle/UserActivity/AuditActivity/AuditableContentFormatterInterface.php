<?php

namespace Rabble\UserBundle\UserActivity\AuditActivity;

use Rabble\UserBundle\Entity\User;

interface AuditableContentFormatterInterface
{
    public function getTranslationId(array $auditPayload, User $user): string;

    public function getTranslationParams(array $auditPayload, User $user): array;
}
