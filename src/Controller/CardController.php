<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Card;
use App\Entity\Subject;
use App\Form\CardType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CardController extends BaseController
{
    public function addAction(Request $request, int $subjectId): Response
    {
        /** @var Subject $subject */
        $subject = $this->getSubjectRepository()->find($subjectId);
        $viewVariables = [];
        $newCard = new Card();
        $newCard->setSubject($subject);
        $form = $this->createForm(CardType::class, $newCard);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Card $card */
            $card = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($card);
            $em->flush();
            return $this->redirectToRoute('addCard', ['subjectId' => $subject->getId()]);
        }
        $viewVariables['newCardForm'] = $form->createView();
        return $this->render('addCard.html.twig', $viewVariables);
    }
}
