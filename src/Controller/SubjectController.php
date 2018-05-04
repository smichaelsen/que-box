<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Subject;
use App\Http\NoContentResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/api/subjects/{subject}", methods="GET")
     * @param Subject $subject
     * @return Response
     */
    public function getSingleSubject(Subject $subject): Response
    {
        return new JsonResponse(['subject' => $subject->getPublicResource()]);
    }

    /**
     * @Route("/api/subjects", methods="POST")
     * @param Request $request
     * @return Response
     */
    public function createSubject(Request $request): Response
    {
        $data = \json_decode($request->getContent(), true);
        $subject = new Subject();
        $subject = $this->fillSubjectWithData($subject, $data);
        if ($this->validateSubject($subject) !== true) {
            return new JsonResponse([], 400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($subject);
        $em->flush();
        return new JsonResponse(['subject' => $subject->getPublicResource()], 201);
    }

    /**
     * @Route("/api/subjects/{subject}", methods="PATCH")
     * @param Request $request
     * @param Subject $subject
     * @return Response
     */
    public function updateSubject(Request $request, Subject $subject): Response
    {
        $data = \json_decode($request->getContent(), true);
        $subject = $this->fillSubjectWithData($subject, $data);
        if ($this->validateSubject($subject) !== true) {
            return new JsonResponse([], 400);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($subject);
        $em->flush();
        return new NoContentResponse();
    }

    /**
     * @Route("/api/subjects/{subject}", methods="DELETE")
     * @param Subject $subject
     * @return Response
     */
    public function deleteSubject(Subject $subject): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($subject);
        $em->flush();
        return new NoContentResponse();
    }

    private function fillSubjectWithData(Subject $subject, array $data): Subject
    {
        if (isset($data['title'])) {
            $subject->setTitle($data['title']);
        }
        if (isset($data['targetCyclesPerDay'])) {
            $subject->setTargetCyclesPerDay((int)$data['targetCyclesPerDay']);
        }
        return $subject;
    }

    private function validateSubject(Subject $subject): bool
    {
        if (empty($subject->getTitle())) {
            return false;
        }
        // check for subject with that name
        $queryBuilder = $this->getSubjectRepository()->createQueryBuilder('s');
        if ($subject->getId() > 0) {
            $queryBuilder
                ->where('s.id != :id AND s.title = :title')
                ->setParameter('id', $subject->getId())
                ->setParameter('title', $subject->getTitle());
        } else {
            $queryBuilder
                ->where('s.title = :title')
                ->setParameter('title', $subject->getTitle());
        }
        $result = $queryBuilder->getQuery()->getResult();
        if (isset($result[0]) && $result[0] instanceof Subject) {
            return false;
        }
        return true;
    }
}
