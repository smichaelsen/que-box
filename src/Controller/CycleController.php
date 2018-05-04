<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Card;
use App\Entity\Cycle;
use App\Entity\Subject;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CycleController extends BaseController
{
    /**
     * @Route("/api/subjects/{subject}/cycles", methods="GET")
     * @param Subject $subject
     * @return Response
     */
    public function getCyclesForSubject(Subject $subject): Response
    {
        $this->getCycleRepository()->deleteUncompletedPastCycles($subject);
        $activeCycles = $this->getCycleRepository()->getActiveCycles($subject);
        if (\count($activeCycles) === 0) {
            $cyclesToCreate = $subject->getTargetCyclesPerDay();
            $cards = $this->getShuffledCards($subject, $cyclesToCreate);
            if (\count($cards) === 0) {
                return new Response('No cards available', 404);
            }
            $em = $this->getDoctrine()->getManager();
            foreach ($cards as $card) {
                $cycle = $this->createNewCycle($subject, $card);
                $em->persist($cycle);
                $activeCycles[] = $cycle;
            }
            $em->flush();
        }
        return new JsonResponse(['cycles' => \array_map(static function (Cycle $cycle) {
            return $cycle->getPublicResource();
        }, $activeCycles)]);
    }

    /**
     * @param Subject $subject
     * @param int $limit
     * @return array|Card[]
     */
    private function getShuffledCards(Subject $subject, int $limit = 0): array
    {
        $cardsPool = new ArrayCollection(
            $this->getCardRepository()->findCardsNotCycledToday($subject)
        );
        $shuffledCards = [];
        while (\count($cardsPool)) {
            $card = $this->pickACard($cardsPool->toArray());
            $cardsPool->removeElement($card);
            $shuffledCards[] = $card;
            if ($limit > 0 && \count($shuffledCards) > $limit) {
                break;
            }
        }
        return $shuffledCards;
    }

    /**
     * @param array|Card[] $cards
     * @return Card
     */
    private function pickACard(array $cards): Card
    {
        $totalWeight = \array_reduce($cards, static function (int $totalWeight, Card $card) {
            $totalWeight += $card->getWeight();
            return $totalWeight;
        }, 0);
        $randomNumber = \mt_rand(1, $totalWeight);
        foreach ($cards as $card) {
            $randomNumber -= $card->getWeight();
            if ($randomNumber <= 0) {
                return $card;
            }
        }
        // can not happen, the loop will always return a card
        return null;
    }

    private function createNewCycle(Subject $subject, Card $card): Cycle
    {
        $cycle = new Cycle();
        $cycle->setCard($card);
        $cycle->setSubject($subject);
        if ($subject->getType() === Subject::TYPE_LANGUAGE) {
            $cycle->setReversed((\mt_rand(0, 1) === 1));
        }
        return $cycle;
    }
}
