<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\Subject;
use Doctrine\ORM\EntityRepository;

class CycleRepository extends EntityRepository
{
    public function countCyclesCompletedToday(Subject $subject): int
    {
        $count = $this->createQueryBuilder('cy')
            ->where('cy.subject = :subject')
            ->setParameter('subject', $subject)
            ->andWhere('cy.completed > :today')
            ->setParameter('today', new \DateTime('today'))
            ->select('COUNT(cy.id) as c')
            ->getQuery()->execute();
        return (int)$count[0]['c'];
    }

    /**
     * Active: Cycles that were created today, but not yet completed
     * @param Subject $subject
     * @return array|Cycle[]
     */
    public function getActiveCycles(Subject $subject): array
    {
        return $this->createQueryBuilder('cy')
            ->select('cy')
            ->where('cy.subject = :subject')
            ->setParameter('subject', $subject)
            ->andWhere('cy.created > :today')
            ->setParameter('today', new \DateTime('today'))
            ->andWhere('cy.completed IS NULL')
            ->getQuery()->execute();
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

    public function deleteUncompletedPastCycles(Subject $subject)
    {
        $this->createQueryBuilder('cy')
            ->delete()
            ->where('cy.subject = :subject')
            ->setParameter('subject', $subject)
            ->andWhere('cy.created < :today')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()->execute();
    }
}
