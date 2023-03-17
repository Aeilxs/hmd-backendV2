<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/users', name: 'app_user_')]
class UserController extends AbstractController
{
    private SerializerInterface $serializer;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private ValidatorInterface $validator;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        SerializerInterface $serializer,
        UserRepository $userRepository,
        ValidatorInterface $validator
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function registration(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setRoles(['ROLE_USER']);

        $errors = $this->validator->validate($user);

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $user->getPassword())
        );

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->userRepository->save($user, true);
        return $this->json([
            'message' => [
                'severity' => 'info',
                'message' => 'Votre compte a été créé avec succès'
            ]
        ], Response::HTTP_CREATED, [], ['groups' => ['user']]);
    }

    #[Route('', name: 'show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        return $this->json([
            'user' => $this->getUser(),
            'message' => [
                'severity' => 'info',
                'message' => 'Les données de l\'utilisateur ont été récupérées avec succès'
            ]
        ], Response::HTTP_OK, [], [
            'groups' => ['user', 'sleep', 'smoke', 'hydration', 'food', 'activity', 'drug']
        ]);
    }
}
