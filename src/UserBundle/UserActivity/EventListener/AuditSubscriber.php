<?php

namespace Rabble\UserBundle\UserActivity\EventListener;

use DH\Auditor\Event\LifecycleEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Rabble\UserBundle\Entity\User;
use Rabble\UserBundle\Entity\UserActivity;
use Rabble\UserBundle\UserActivity\AuditActivity\AuditableContentFormatterInterface;
use Rabble\UserBundle\UserActivity\AuditActivity\AuditActivityType;
use Rabble\UserBundle\UserActivity\AuditActivity\AuditSubjectInterface;
use Rabble\UserBundle\UserActivity\AuditActivity\TemplatableAuditSubjectInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuditSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private $removedEntities = [];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LifecycleEvent::class => 'onAuditEvent',
        ];
    }

    public function onAuditEvent(LifecycleEvent $event)
    {
        $payload = $event->getPayload();
        if (null === $payload['blame_id']) {
            return;
        }
        $entity = $this->entityManager->find($payload['entity'], $payload['object_id']);
        $blameUser = $this->entityManager->find('User', $payload['blame_id']);
        if (null === $entity && 'remove' === $payload['type'] && isset($this->removedEntities[$payload['entity']][$payload['object_id']])) {
            $entity = $this->removedEntities[$payload['entity']][$payload['object_id']];
        }
        if (null === $entity || !$blameUser instanceof User) {
            return;
        }
        $activityPayload = null;
        if ($entity instanceof TemplatableAuditSubjectInterface) {
            $activityPayload = ['templateName' => $entity->getTemplateName()];
        }
        $userActivity = new UserActivity();
        if ($entity instanceof AuditSubjectInterface) {
            $userActivity->setTranslationId(sprintf('audit.%s.%s', $payload['type'], $entity->getAuditSubjectType()));
            $userActivity->setTranslationParams(['%subject%' => $entity->getAuditSubject()]);
        } elseif ($entity instanceof AuditableContentFormatterInterface) {
            $userActivity->setTranslationId($entity->getTranslationId($payload, $blameUser));
            $userActivity->setTranslationParams($entity->getTranslationParams($payload, $blameUser));
        } else {
            return;
        }
        $userActivity->setType(AuditActivityType::TYPE_NAME);
        $userActivity->setUser($blameUser);
        $userActivity->setPayload($activityPayload);
        $this->entityManager->persist($userActivity);
        $this->entityManager->flush();
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityClass = get_class($entity);
        if (!isset($this->removedEntities[$entityClass])) {
            $this->removedEntities[$entityClass] = [];
        }
        $objectMetadata = $this->entityManager->getClassMetadata($entityClass);
        $this->removedEntities[$entityClass][array_values($objectMetadata->getIdentifierValues($entity))[0]] = $entity;
    }
}
