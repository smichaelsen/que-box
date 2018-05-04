<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Card;
use App\Entity\Subject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/api/cards", methods="POST")
     * @param Request $request
     * @return Response
     */
    public function createCard(Request $request): Response
    {
        $data = \json_decode($request->getContent(), true);
        $card = new Card();
        $card = $this->fillCardWithData($card, $data);
        if ($this->validateCard($card) !== true) {
            return new JsonResponse([], 400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($card);
        $em->flush();
        return new JsonResponse(['card' => $card->getPublicResource()], 201);
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

    private function fillCardWithData(Card $card, array $data): Card
    {
        if (isset($data['backsideContent'])) {
            $card->setBacksideContent($data['backsideContent']);
        }
        if (isset($data['frontsideContent'])) {
            $card->setFrontsideContent($data['frontsideContent']);
        }
        if (isset($data['subjectId'])) {
            $subject = $this->getSubjectRepository()->find((int)$data['subjectId']);
            if ($subject instanceof Subject) {
                $card->setSubject($subject);
            }
        }
        return $card;
    }

    private function validateCard(Card $card): bool
    {
        if (!$card->getSubject() instanceof Subject) {
            return false;
        }
        return true;
    }
}
