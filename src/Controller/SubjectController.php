<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Subject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/api/subjects", methods="POST")
     * @param Request $request
     * @return Response
     */
    public function createSubject(Request $request): Response
    {
        $data = \json_decode($request->getContent(), true);

        // fill model with data
        /** @var Subject $subject */
        $subject = (function () use ($data): Subject {
            $subject = new Subject();
            if (isset($data['title'])) {
                $subject->setTitle($data['title']);
            }
            return $subject;
        })();

        // validate model
        $valid = (function () use ($subject): bool {
            // title is not empty
            if (empty($subject->getTitle())) {
                return false;
            }
            // check for subject with that name
            $existingSubject = $this->getSubjectRepository()->findOneBy(['title' => $subject->getTitle()]);
            if ($existingSubject instanceof Subject) {
                return false;
            }
            return true;
        })();

        if ($valid !== true) {
            return new JsonResponse([], 400);
        }

        // persist model
        $em = $this->getDoctrine()->getManager();
        $em->persist($subject);
        $em->flush();
        return new JsonResponse(['subject' => $subject->getPublicResource()], 201);
    }
}
