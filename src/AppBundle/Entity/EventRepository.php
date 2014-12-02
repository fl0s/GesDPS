<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends EntityRepository
{
    public function getLastCOA()
    {
        $year = date('Y');

        return $this->getEntityManager()
            ->createQuery(
                'SELECT e.coa 
                FROM AppBundle:Event e 
                WHERE e.coa_year = :year
                ORDER BY e.coa DESC'
            )
            ->setParameter('year', $year)
            ->setMaxResults(1)
            ->getSingleScalarResult();
    }

}