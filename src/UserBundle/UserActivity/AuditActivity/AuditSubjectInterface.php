<?php

namespace Rabble\UserBundle\UserActivity\AuditActivity;

interface AuditSubjectInterface
{
    /**
     * Returns the name of the subject that has been modified.
     */
    public function getAuditSubject(): string;

    /**
     * We need a subject type in order to generate a safe translation id.
     */
    public function getAuditSubjectType(): string;
}
