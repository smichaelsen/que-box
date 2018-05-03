<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Card;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends BaseController
{
    /**
     * @Route("/api/subjects/{subjectId}/scoreCounts", methods="GET")
     * @param int $subjectId
     * @return Response
     */
    public function getScoreCountsOfSubject(int $subjectId): Response
    {
        $queryBuilder = $this->getCardRepository()->createQueryBuilder('c');
        $result = $queryBuilder
            ->select('COUNT(c.id) as count', 'c.score')
            ->where('c.subject = :subjectId')
            ->setParameter('subjectId', $subjectId)
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
     * @Route("/api/subjects/{subjectId}/cards/{score}", methods="GET")
     * @param int $subjectId
     * @param int $score
     * @return Response
     */
    public function getCardsOfSubjectByScore(int $subjectId, int $score): Response
    {
        $cards = $this->getCardRepository()->findBy(['subject' => $subjectId, 'score' => $score]);
        return new JsonResponse(['cards' => \array_map(static function (Card $card) {
            return $card->getPublicResource();
        }, $cards)]);
    }
}
