<?php

namespace AppBundle\Repository;

/**
 * EventRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByGoogleId($id)
    {
        return $this->createQueryBuilder('event')
            ->andWhere('event.googleId = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }
}
