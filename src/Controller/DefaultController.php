<?php
namespace App\Controller;

use App\Entity\Card;
use App\Entity\Cycle;
use App\Error\NoCardsAvailableException;
use App\Form\CardType;
use App\Repository\CardRepository;
use App\Repository\CycleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    public function indexAction(): Response
    {
        $cardRepository = $this->getCardRepository();
        $anyCard = $cardRepository->findOneBy([]);
        if (!$anyCard instanceof Card) {
            return $this->redirectToRoute('firstRun');
        }
        return $this->render('index.html.twig');
    }

    public function firstRunAction(Request $request): Response
    {
        $viewVariables = [];
        $newShop = new Card();
        $form = $this->createForm(CardType::class, $newShop);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Card $card */
            $shop = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($shop);
            $em->flush();
            return $this->redirectToRoute('index');
        }
        $viewVariables['newCardForm'] = $form->createView();
        return $this->render('addCard.html.twig', $viewVariables);
    }

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

    protected function getCardRepository(): CardRepository
    {
        /** @var CardRepository $cardRepository */
        $cardRepository = $this->getDoctrine()->getManager()->getRepository(Card::class);
        return $cardRepository;
    }

    protected function getCycleRepository(): CycleRepository
    {
        /** @var CycleRepository $cycleRepository */
        $cycleRepository = $this->getDoctrine()->getManager()->getRepository(Cycle::class);
        return $cycleRepository;
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