<?php
declare(strict_types=1);
namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class CardRepository extends EntityRepository
{
    public function findCardsNotCycledToday(): array
    {
        $query = $this->createQueryBuilder('ca')
            ->andWhere('ca.lastCycle < :today OR ca.lastCycle IS NULL')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
        ;
        return $query->execute();
    }
}
