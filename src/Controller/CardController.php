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
     * @Route("/api/subjects/{subjectId}/cards", methods="GET")
     * @param int $subjectId
     * @return Response
     */
    public function getCardsOfSubject(int $subjectId): Response
    {
        $cards = $this->getCardRepository()->findBy(['subject' => $subjectId]);
        return new JsonResponse(['cards' => \array_map(static function (Card $card) {
            return $card->getPublicResource();
        }, $cards)]);
    }
}
