<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Subject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubjectController extends BaseController
{
    /**
     * @Route("/api/subjects", name="subjects")
     * @return Response
     */
    public function getAllSubjects(): Response
    {
        $subjects = $this->getSubjectRepository()->findAll();
        return new JsonResponse(['subjects' => array_map(function (Subject $subject) {
            return $subject->getPublicResource();
        }, $subjects)]);
    }
}
