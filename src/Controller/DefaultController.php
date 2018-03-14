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
        $viewVariables = [];
        $subjects = $this->getSubjectRepository()->findAll();
        $viewVariables['subjects'] = $subjects;
        return $this->render('index.html.twig', $viewVariables);
    }

    public function firstRunAction(Request $request): Response
    {
        $viewVariables = [];
        $newCard = new Card();
        $form = $this->createForm(CardType::class, $newCard);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Card $card */
            $card = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($card);
            $em->flush();
            return $this->redirectToRoute('index');
        }
        $viewVariables['newCardForm'] = $form->createView();
        return $this->render('addCard.html.twig', $viewVariables);
    }
}
