<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Subject;
use Doctrine\ORM\EntityRepository;

class CycleRepository extends EntityRepository
{
    public function countCyclesCompletedToday(Subject $subject): int
    {
        $count = $this->createQueryBuilder('cy')
            ->andWhere('cy.subject = :subject')
            ->setParameter('subject', $subject)
            ->andWhere('cy.completed > :today')
            ->setParameter('today', new \DateTime('today'))
            ->select('COUNT(cy.id) as c')
            ->getQuery()->execute();
        return (int)$count[0]['c'];
    }

    public function getCyclesCompletedToday(Subject $subject): array
    {
        return $this->createQueryBuilder('cy')
            ->select('cy')
            ->andWhere('cy.subject = :subject')
            ->setParameter('subject', $subject)
            ->andWhere('cy.completed > :today')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()->execute();
    }
}
