<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/users', name: 'app_user_')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private SerializerInterface $serializer;
    public function __construct(UserRepository $userRepository, SerializerInterface $serializer)
    {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setRoles('ROLE_USER');
        dump($user);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
}
