<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findByDate()
    {
        return $this->findBy([], ['startDate' => 'ASC']);
    }

    public function findByType()
    {
        return $this->findBy([], ['type' => 'ASC']);
    }

    public function findByTitle()
    {
        return $this->findBy([], ['title' => 'ASC']);
    }

    public function findByLocation()
    {
        return $this->findBy([], ['location' => 'ASC']);
    }
    public function findEventsBetweenDates(\DateTime $start, \DateTime $end)
{
    return $this->createQueryBuilder('e')
        ->where('e.startDate >= :start AND e.endDate <= :end')
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->orderBy('e.startDate', 'ASC')
        ->getQuery()
        ->getResult();
}
}