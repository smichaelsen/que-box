<?php
namespace App\Controller;

use App\Entity\Card;
use App\Entity\Cycle;
use App\Error\NoCardsAvailableException;
use Symfony\Component\HttpFoundation\Response;

class CycleController extends BaseController
{

    public function cycleAction(): Response
    {
        $viewVariables = [];
        $cycle = $this->getCycleRepository()->findOneBy(['result' => null]);
        if (!$cycle instanceof Cycle) {
            try {
                $cycle = $this->createNewCycle();
            } catch (NoCardsAvailableException $e) {
                return $this->render('noCycleAvailable.html.twig');
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($cycle);
            $em->flush();
        }
        $viewVariables['cycle'] = $cycle;
        return $this->render('cycle.html.twig', $viewVariables);
    }

    public function cycleSucceedAction(): Response
    {
        $cycle = $this->getCycleRepository()->findOneBy(['result' => null]);
        assert($cycle instanceof Cycle, 'Cycle to succeed could not be loaded');
        $cycle->succeed();
        $em = $this->getDoctrine()->getManager();
        $em->persist($cycle);
        $em->flush();
        return $this->redirectToRoute('cycle');
    }

    public function cycleFailAction(): Response
    {
        $cycle = $this->getCycleRepository()->findOneBy(['result' => null]);
        assert($cycle instanceof Cycle, 'Cycle to fail could not be loaded');
        $cycle->fail();
        $em = $this->getDoctrine()->getManager();
        $em->persist($cycle);
        $em->flush();
        return $this->redirectToRoute('cycle');
    }

    /**
     * @return Cycle
     * @throws NoCardsAvailableException
     */
    protected function createNewCycle(): Cycle
    {
        $cardsNotCycledToday = $this->getCardRepository()->findCardsNotCycledToday();
        if (count($cardsNotCycledToday) === 0) {
            throw new NoCardsAvailableException();
        }
        $card = $this->pickACard($cardsNotCycledToday);
        $cycle = new Cycle();
        $cycle->setCard($card);
        return $cycle;
    }



    /**
     * @param array|Card[] $cards
     * @return Card
     */
    private function pickACard(array $cards): Card
    {
        $totalWeight = array_reduce($cards, function (int $totalWeight, Card $card) {
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
}