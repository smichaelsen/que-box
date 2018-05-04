<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Card;
use App\Entity\Subject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends BaseController
{
    /**
     * @Route("/api/cards/{card}", methods="GET")
     * @param Card $card
     * @return Response
     */
    public function getSingleCard(Card $card): Response
    {
        return new JsonResponse($card->getPublicResource());
    }

    /**
     * @Route("/api/subjects/{subject}/scoreCounts", methods="GET")
     * @param Subject $subject
     * @return Response
     */
    public function getScoreCountsOfSubject(Subject $subject): Response
    {
        $queryBuilder = $this->getCardRepository()->createQueryBuilder('c');
        $result = $queryBuilder
            ->select('COUNT(c.id) as count', 'c.score')
            ->where('c.subject = :subjectId')
            ->setParameter('subjectId', $subject->getId())
            ->groupBy('c.score')
            ->orderBy('c.score')
            ->getQuery()
            ->getResult();
        $scoreCounts = [];
        foreach ($result as $resultItem) {
            $scoreCounts[$resultItem['score']] = $resultItem['count'];
        }
        ksort($scoreCounts);
        return new JsonResponse(['scoreCounts' => $scoreCounts]);
    }

    /**
     * @Route("/api/subjects/{subject}/cards/{score}", methods="GET")
     * @param Subject $subject
     * @param int $score
     * @return Response
     */
    public function getCardsOfSubjectByScore(Subject $subject, int $score): Response
    {
        $cards = $this->getCardRepository()->findBy(['subject' => $subject, 'score' => $score]);
        return new JsonResponse(['cards' => \array_map(static function (Card $card) {
            return $card->getPublicResource();
        }, $cards)]);
    }
}
