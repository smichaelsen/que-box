<?php
namespace App\Controller;

use App\Entity\Cycle;
use App\Entity\Subject;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends BaseController
{
    public function indexAction(): Response
    {
        $viewVariables = [];
        $subjects = [];
        foreach ($this->getSubjectRepository()->findAll() as $subject) {
            /** @var Subject $subject */
            /** @var Cycle $lastCycle */
            $lastCycle = $this->getCycleRepository()->findOneBy(['subject' => $subject], ['completed' => 'DESC']);
            $subjects[] = [
                'entity' => $subject,
                'stats' => [
                    'cardsCount' => $this->getCardRepository()->count(['subject' => $subject]),
                    'score' => $this->getCardRepository()->sumScore($subject),
                    'lastCycleTime' => $lastCycle instanceof Cycle ? $lastCycle->getCompleted() : null,
                ],
            ];
        }
        $viewVariables['subjects'] = $subjects;
        return $this->render('index.html.twig', $viewVariables);
    }
}
