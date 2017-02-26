<?php

namespace AppBundle\Security;

use AppBundle\Entity\Admin;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Common\EventSubscriber;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class PasswordListener implements EventSubscriber
{
    private $encoder;

    public function __construct(UserPasswordEncoder $encoder)
    {
        $this->encoder = $encoder;
    }
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
        );
    }
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User || $entity instanceof Admin && $entity->getPlainPassword()) {
            $this->encodePassword($entity);
        }
    }
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User || $entity instanceof Admin && $entity->getPlainPassword()) {
            $this->encodePassword($entity);
        }
    }
    private function encodePassword($entity)
    {
        $password = $entity->getPlainPassword();
        $encoded = $this->encoder->encodePassword($entity, $password);
        $entity->setPassword($encoded);
    }
}
