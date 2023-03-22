<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
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

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function updateUser(Request $request, User $user): JsonResponse
    {
        $requestData = $request->getContent();
        $this->serializer->deserialize($requestData, User::class, 'json', ['object_to_populate' => $user]);

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->userRepository->save($user, true);

        return $this->json([
            'user' => $this->getUser(),
            'message' => [
                'severity' => 'info',
                'message' => 'Votre compte a été mis à jour avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['user']]);
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

    #[Route('/{id}', name: 'fetchUser', methods: ['GET'])]
    public function fetchUser(UserRepository $userRepository, int $id): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        if ($this->getUser() !== $user) {
            throw new AccessDeniedException();
        }

        return $this->json([
            'message' => [
                'severity' => 'info',
                'message' => 'Les données de l\'utilisateur ont été récupérées avec succès',
            ],
            'user' => [
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'gender' => $user->getGender(),
                'roles' => $user->getRoles(),
                'size' => $user->getSize(),
                'weight' => $user->getWeight(),
                'dateOfBirth' => $user->getDateOfBirth(),
                'activities' => $user->getSortedCollection('activities'),
                'drugs' => $user->getSortedCollection('drugs'),
                'foods' => $user->getSortedCollection('foods'),
                'hydrations' => $user->getSortedCollection('hydrations'),
                'sleeps' => $user->getSortedCollection('sleeps'),
                'smokes' => $user->getSortedCollection('smokes'),
            ],
        ], Response::HTTP_OK, [], [
            'groups' => ['user', 'sleep', 'smoke', 'hydration', 'food', 'activity', 'drug']
        ]);
    }
}
