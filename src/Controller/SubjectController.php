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
     * @Route("/api/subjects", methods="GET")
     * @return Response
     */
    public function getAllSubjects(): Response
    {
        $subjects = $this->getSubjectRepository()->findAll();
        return new JsonResponse(['subjects' => \array_map(static function (Subject $subject) {
            return $subject->getPublicResource();
        }, $subjects)]);
    }

    /**
     * @Route("/api/subjects/{subjectId}", methods="GET")
     * @param int $subjectId
     * @return Response
     */
    public function getSingleSubject(int $subjectId): Response
    {
        $subject = $this->getSubjectRepository()->findOneBy(['id' => $subjectId]);
        if ($subject instanceof Subject) {
            return new JsonResponse(['subject' => $subject->getPublicResource()]);
        }
        throw new NotFoundHttpException('The subject could not be loaded');
    }
}
