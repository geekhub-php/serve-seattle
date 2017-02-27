<?php

namespace AppBundle\Security;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;

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

        if ($entity instanceof UserInterface && $entity->getPlainPassword()) {
            $this->encodePassword($entity);
        }
    }
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof UserInterface && $entity->getPlainPassword()) {
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
