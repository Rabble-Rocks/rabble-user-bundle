<?php

namespace Rabble\UserBundle\EventListener\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Rabble\UserBundle\Entity\User;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class AvatarListener
{
    private UploaderHelper $uploaderHelper;

    private CacheManager $cacheManager;

    public function __construct(UploaderHelper $uploaderHelper, CacheManager $cacheManager)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->cacheManager = $cacheManager;
    }

    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof User) {
            $this->loadAvatar($entity);
        }
    }

    private function loadAvatar(User $user)
    {
        if (null !== $user->getImage() && null !== $user->getImage()->getName()) {
            $avatar = $this->uploaderHelper->asset($user, 'imageFile');
            $avatar = $this->cacheManager->getBrowserPath($avatar, 'rabble_square');
            $user->setAvatar($avatar);
        } else {
            $user->setAvatar(null);
        }
    }
}
