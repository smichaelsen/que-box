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
        return $this->completeCycle($subjectId, 'succeed');
    }

    public function cycleFailAction(int $subjectId): Response
    {
        return $this->completeCycle($subjectId, 'fail');
    }

    public function cycleSummaryAction(int $subjectId): Response
    {
        /** @var Subject $subject */
        $subject = $this->getSubjectRepository()->find($subjectId);
        $viewVariables = [];
        $cyclesCompletedToday = $this->getCycleRepository()->getCyclesCompletedToday($subject);
        $viewVariables['cyclesCompletedToday'] = [
            'succeeded' => \array_filter($cyclesCompletedToday, static function (Cycle $cycle) {
                return $cycle->getResult() === Cycle::RESULT_SUCCESS;
            }),
            'failed' => \array_filter($cyclesCompletedToday, static function (Cycle $cycle) {
                return $cycle->getResult() === Cycle::RESULT_FAILURE;
            }),
        ];
        return $this->render('cycleSummary.html.twig', $viewVariables);
    }

    protected function completeCycle(int $subjectId, string $verb): Response
    {
        \assert(\in_array($verb, ['succeed', 'fail'], true), 'Verb has to be succeed or fail');
        /** @var Subject $subject */
        $subject = $this->getSubjectRepository()->find($subjectId);
        $cycle = $this->getCycleRepository()->findOneBy(['result' => null, 'subject' => $subject]);
        \assert($cycle instanceof Cycle, 'Cycle to fail could not be loaded');
        if ($verb === 'succeed') {
            $cycle->succeed();
        } else {
            $cycle->fail();
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($cycle);
        $em->flush();
        $cyclesCompletedToday = $this->getCycleRepository()->countCyclesCompletedToday($subject);
        $cardsNotCycledToday = $this->getCardRepository()->findCardsNotCycledToday($subject);

        /**
         * show summary if
         * - the target cycles per day are reached
         * - after that another half of the target is reached
         * - no cards are left to cycle
         */
        if (
            $cyclesCompletedToday === $subject->getTargetCyclesPerDay() ||
            (
                $cyclesCompletedToday > $subject->getTargetCyclesPerDay() &&
                (int)(($cyclesCompletedToday - $subject->getTargetCyclesPerDay()) % \ceil($subject->getTargetCyclesPerDay() / 2)) === 0
            ) ||
            \count($cardsNotCycledToday) === 0
        ) {
            return $this->redirectToRoute('cycleSummary', ['subjectId' => $subject->getId()]);
        }

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
        if ($subject->getType() === Subject::TYPE_LANGUAGE) {
            $cycle->setReversed((\mt_rand(0, 1) === 1));
        }
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
