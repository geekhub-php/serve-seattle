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
        return $this->createQueryBuilder('e')
            ->andWhere('e.googleId = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function selectNotExpiredByUser($user, $dashboard = false)
    {
        $qb = $this->createQueryBuilder('e')
            ->join('e.user', 'user')
            ->andWhere('user = :user')
            ->setParameter('user', $user)
            ->andWhere('e.expiredAt >= :date')
            ->setParameter('date', new \DateTime());

        if ($dashboard) {
            $qb->setMaxResults(2);
        }

        return $qb->getQuery()->getResult();
    }
}
