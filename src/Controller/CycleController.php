<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Card;
use App\Entity\Cycle;
use App\Entity\Subject;
use App\Error\NoCardsAvailableException;
use Symfony\Component\HttpFoundation\Response;

class CycleController extends BaseController
{
    public function cycleAction(int $subjectId): Response
    {
        /** @var Subject $subject */
        $subject = $this->getSubjectRepository()->find($subjectId);
        $viewVariables = [];
        $cycle = $this->getCycleRepository()->findOneBy(['result' => null, 'subject' => $subject]);
        if (!$cycle instanceof Cycle) {
            try {
                $cycle = $this->createNewCycle($subject);
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

    public function cycleSucceedAction(int $subjectId): Response
    {
        /** @var Subject $subject */
        $subject = $this->getSubjectRepository()->find($subjectId);
        $cycle = $this->getCycleRepository()->findOneBy(['result' => null, 'subject' => $subject]);
        \assert($cycle instanceof Cycle, 'Cycle to succeed could not be loaded');
        $cycle->succeed();
        $em = $this->getDoctrine()->getManager();
        $em->persist($cycle);
        $em->flush();
        return $this->redirectToRoute('cycle', ['subjectId' => $subject->getId()]);
    }

    public function cycleFailAction(int $subjectId): Response
    {
        /** @var Subject $subject */
        $subject = $this->getSubjectRepository()->find($subjectId);
        $cycle = $this->getCycleRepository()->findOneBy(['result' => null, 'subject' => $subject]);
        \assert($cycle instanceof Cycle, 'Cycle to fail could not be loaded');
        $cycle->fail();
        $em = $this->getDoctrine()->getManager();
        $em->persist($cycle);
        $em->flush();
        return $this->redirectToRoute('cycle', ['subjectId' => $subject->getId()]);
    }

    /**
     * @param Subject $subject
     * @throws NoCardsAvailableException
     * @return Cycle
     */
    protected function createNewCycle(Subject $subject): Cycle
    {
        $cardsNotCycledToday = $this->getCardRepository()->findCardsNotCycledToday($subject);
        if (\count($cardsNotCycledToday) === 0) {
            throw new NoCardsAvailableException();
        }
        $card = $this->pickACard($cardsNotCycledToday);
        $cycle = new Cycle();
        $cycle->setCard($card);
        $cycle->setSubject($subject);
        return $cycle;
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
}
