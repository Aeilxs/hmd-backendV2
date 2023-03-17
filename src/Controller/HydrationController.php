<?php

namespace App\Controller;

use App\Entity\Hydration;
use App\Repository\HydrationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/hydrations', name: 'app_user_hydrations_')]
class HydrationController extends AbstractController
{
    private SerializerInterface $serializer;
    private HydrationRepository $hydrationRepository;
    private ValidatorInterface $validator;

    public function __construct(
        HydrationRepository $hydrationRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->hydrationRepository = $hydrationRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $hydration = $this->serializer->deserialize($request->getContent(), Hydration::class, 'json');
        $hydration->setUser($this->getUser());
        $errors = $this->validator->validate($hydration);


        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors,
                'message' => [
                    'severity' => 'error',
                    'message' => 'Votre consommation d\'eau n\'a pas pu être ajouté'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->hydrationRepository->save($hydration, true);

        return $this->json([
            'hydration' => $hydration,
            'hydrations' => $hydration->getUser()->getSortedCollection('hydrations'),
            'message' => [
                'severity' => 'info',
                'message' => 'Votre consommation d\'eau a été enregistré avec succès'
            ]
        ], Response::HTTP_CREATED, [], ['groups' => ['hydration']]);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Request $request, Hydration $hydration): JsonResponse
    {
        $updatedHydration = $this->serializer->deserialize($request->getContent(), Hydration::class, 'json', ['object_to_populate' => $hydration]);

        $errors = $this->validator->validate($updatedHydration);

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->hydrationRepository->save($updatedHydration, true);

        return $this->json([
            'hydration' => $updatedHydration,
            'hydrations' => $updatedHydration->getUser()->getSortedCollection('hydrations'),
            'message' => [
                'severity' => 'info',
                'message' => 'Votre consommation d\'eau a été mis à jour avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['hydration']]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Hydration $hydration): JsonResponse
    {
        $this->hydrationRepository->remove($hydration, true);

        return $this->json([
            'hydration' => $hydration,
            'hydrations' => $hydration->getUser()->getSortedCollection('hydrations'),
            'message' => [
                'severity' => 'info',
                'message' => 'Votre consommation d\'eau a été supprimé avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['hydration']]);
    }
}
