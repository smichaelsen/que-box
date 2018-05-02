<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Subject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SubjectController extends BaseController
{
    /**
     * @Route("/api/subjects", name="getAllSubjects")
     * @return Response
     */
    public function getAllSubjects(): Response
    {
        $subjects = $this->getSubjectRepository()->findAll();
        return new JsonResponse(['subjects' => array_map(function (Subject $subject) {
            return $subject->getPublicResource();
        }, $subjects)]);
    }

    /**
     * @Route("/api/subjects/{subjectId}", name="getSingleSubject")
     * @return Response
     */
    public function getSingleSubject(int $subjectId): Response
    {
        $subject = $this->getSubjectRepository()->findOneBy(['id' => $subjectId]);
        if ($subject instanceof Subject) {
            return new JsonResponse(['subject' => $subject->getPublicResource()]);
        } else {
            throw new NotFoundHttpException('The subject could not be loaded');
        }
    }
}
