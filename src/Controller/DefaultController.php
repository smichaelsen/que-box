<?php
namespace App\Controller;

use App\Entity\Card;
use App\Form\CardType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends BaseController
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
}
