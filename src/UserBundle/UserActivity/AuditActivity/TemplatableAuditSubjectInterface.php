<?php

namespace Rabble\UserBundle\UserActivity\AuditActivity;

interface TemplatableAuditSubjectInterface
{
    public function getTemplateName(): string;
}
