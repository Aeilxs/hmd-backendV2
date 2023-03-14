<?php

namespace App\Controller;

use App\Entity\Smoke;
use App\Repository\SmokeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/smokes', name: 'app_user_smokes_')]
class SmokeController extends AbstractController
{
    private SerializerInterface $serializer;
    private SmokeRepository $smokeRepository;
    private ValidatorInterface $validator;

    public function __construct(
        SmokeRepository $smokeRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->smokeRepository = $smokeRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $smoke = $this->serializer->deserialize($request->getContent(), Smoke::class, 'json');

        $errors = $this->validator->validate($smoke);
        $smoke->setUser($this->getUser());

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors,
                'message' => [
                    'severity' => 'error',
                    'message' => 'Votre consommation n\'a pas pu être ajouté'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->smokeRepository->save($smoke, true);

        return $this->json([
            'smoke' => $smoke,
            'message' => [
                'severity' => 'info',
                'message' => 'Votre consommation a été enregistrée avec succès'
            ]
        ], Response::HTTP_CREATED, [], ['groups' => ['smoke']]);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Request $request, Smoke $smoke): JsonResponse
    {
        $updatedSmoke = $this->serializer->deserialize($request->getContent(), Smoke::class, 'json', ['object_to_populate' => $smoke]);
        $errors = $this->validator->validate($updatedSmoke);

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }
        $this->smokeRepository->save($updatedSmoke, true);

        return $this->json([
            'smoke' => $updatedSmoke,
            'message' => [
                'severity' => 'info',
                'message' => 'Votre consommation a été mise à jour avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['smoke']]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Smoke $smoke): JsonResponse
    {
        $smokeId = $smoke->getId();
        $this->smokeRepository->remove($smoke, true);

        return $this->json([
            'id' => $smokeId,
            'message' => [
                'severity' => 'info',
                'message' => 'Votre temps consommation a été supprimée avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['smoke']]);
    }
}
