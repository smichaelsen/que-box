<?php
namespace App\Controller;

use App\Entity\Subject;
use App\Form\SubjectType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubjectController extends BaseController
{

    public function addAction(Request $request): Response
    {
        $viewVariables = [];
        $newSubject = new Subject();
        $form = $this->createForm(SubjectType::class, $newSubject);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Subject $subject */
            $subject = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($subject);
            $em->flush();
            return $this->redirectToRoute('index');
        }
        $viewVariables['newSubjectForm'] = $form->createView();
        return $this->render('addSubject.html.twig', $viewVariables);
    }
}