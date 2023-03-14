<?php

namespace App\Controller;

use App\Entity\Food;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FoodRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/foods', name: 'app_user_foods_')]
class FoodController extends AbstractController
{
    private SerializerInterface $serializer;
    private FoodRepository $foodRepository;
    private ValidatorInterface $validator;

    public function __construct(
        FoodRepository $foodRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->foodRepository = $foodRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $food = $this->serializer->deserialize($request->getContent(), Food::class, 'json');
        $errors = $this->validator->validate($food);
        $food->setUser($this->getUser());

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors,
                'message' => [
                    'severity' => 'error',
                    'message' => 'Votre consommation n\'a pas pu être ajouté'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->foodRepository->save($food, true);

        return $this->json([
            'food' => $food,
            'message' => [
                'severity' => 'info',
                'message' => 'Votre consommation a été enregistré avec succès'
            ]
        ], Response::HTTP_CREATED, [], ['groups' => ['food']]);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Request $request, Food $food): JsonResponse
    {
        $updatedFood = $this->serializer->deserialize($request->getContent(), Food::class, 'json', ['object_to_populate' => $food]);

        $errors = $this->validator->validate($updatedFood);

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->foodRepository->save($updatedFood, true);

        return $this->json([
            'food' => $updatedFood,
            'message' => [
                'severity' => 'info',
                'message' => 'Votre consommation a été mis à jour avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['food']]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Food $food): JsonResponse
    {
        $foodId = $food->getId();
        $this->foodRepository->remove($food, true);

        return $this->json([
            'id' => $foodId,
            'message' => [
                'severity' => 'info',
                'message' => 'Votre consommation a été supprimé avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['food']]);
    }
}
