<?php

namespace App\Controller;

use App\Entity\FoodCategory;
use App\Repository\FoodCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/foods-categories', name: 'app_user_categories_')]
class FoodCategoryController extends AbstractController
{
    private SerializerInterface $serializer;
    private FoodCategoryRepository $foodCategoryRepository;
    private ValidatorInterface $validator;

    public function __construct(
        FoodCategoryRepository $foodCategoryRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->foodCategoryRepository = $foodCategoryRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('', name: 'food_cat_show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        $categories = $this->foodCategoryRepository->findAll();

        return $this->json([
            'data' => $categories
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $category =  $this->serializer->deserialize($request->getContent(), FoodCategory::class, 'json');
        $errors = $this->validator->validate($category);

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }
        $this->foodCategoryRepository->save($category, true);

        return $this->json([
            'message' => [
                'severity' => 'info',
                'message' => 'Catégorie créée avec succès'
            ]
        ], Response::HTTP_CREATED);
    }
}
