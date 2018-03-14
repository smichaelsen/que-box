<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Subject;
use Doctrine\ORM\EntityRepository;

class CardRepository extends EntityRepository
{
    public function findCardsNotCycledToday(Subject $subject): array
    {
        $query = $this->createQueryBuilder('ca')
            ->andWhere('ca.lastCycle < :today OR ca.lastCycle IS NULL')
            ->setParameter('today', new \DateTime('today'))
            ->andWhere('ca.subject = :subject')
            ->setParameter('subject', $subject)
            ->getQuery()
        ;
        return $query->execute();
    }
}
