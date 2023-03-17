<?php

namespace App\Controller;


use App\Entity\Activity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ActivityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/activities', name: 'app_user_activities_')]
class ActivityController extends AbstractController
{
    private SerializerInterface $serializer;
    private ActivityRepository $activityRepository;
    private ValidatorInterface $validator;

    public function __construct(
        ActivityRepository $activityRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->activityRepository = $activityRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {

        $activity = $this->serializer->deserialize($request->getContent(), Activity::class, 'json');

        $errors = $this->validator->validate($activity);

        $activity->setUser($this->getUser());

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors,
                'message' => [
                    'severity' => 'error',
                    'message' => 'Votre activité n\'a pas pu être ajouté'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->activityRepository->save($activity, true);

        return $this->json([
            'activity' => $activity,
            'activities' => $activity->getUser()->getActivities(),
            'message' => [
                'severity' => 'info',
                'message' => 'Votre activité a été enregistré avec succès'
            ]
        ], Response::HTTP_CREATED, [], ['groups' => ['activity']]);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Request $request, activity $activity): JsonResponse
    {
        $updatedactivity = $this->serializer->deserialize($request->getContent(), Activity::class, 'json', ['object_to_populate' => $activity]);

        $errors = $this->validator->validate($updatedactivity);

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->activityRepository->save($updatedactivity, true);

        return $this->json([
            'activity' => $updatedactivity,
            'activities' => $updatedactivity->getUser()->getActivities(),
            'message' => [
                'severity' => 'info',
                'message' => 'Votre activité a été mis à jour avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['activity']]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Activity $activity): JsonResponse
    {
        $activityId = $activity->getId();
        $this->activityRepository->remove($activity, true);

        return $this->json([
            'activities' => $activity->getUser()->getActivities(),
            'message' => [
                'severity' => 'info',
                'message' => 'Votre activité a été supprimé avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['activity']]);
    }
}
