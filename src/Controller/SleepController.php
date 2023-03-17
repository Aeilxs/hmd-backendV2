<?php

namespace App\Controller;

use App\Entity\Sleep;
use App\Entity\User;
use App\Repository\SleepRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_USER')]
#[Route('/api/sleeps', name: 'app_user_sleeps_')]
class SleepController extends AbstractController
{
    private SerializerInterface $serializer;
    private SleepRepository $sleepRepository;
    private ValidatorInterface $validator;

    public function __construct(
        SleepRepository $sleepRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->sleepRepository = $sleepRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $sleep = $this->serializer->deserialize($request->getContent(), Sleep::class, 'json');
        $errors = $this->validator->validate($sleep);
        $sleep->setUser($this->getUser());


        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors,
                'message' => [
                    'severity' => 'error',
                    'message' => 'Votre temps de sommeil n\'a pas pu être ajouté'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }
        $this->sleepRepository->save($sleep, true);
        return $this->json([
            'sleep' => $sleep,
            'sleeps' => $sleep->getUser()->getSortedCollection('sleeps'),
            'message' => [
                'severity' => 'info',
                'message' => 'Votre temps de sommeil a été enregistré avec succès'
            ]
        ], Response::HTTP_CREATED, [], ['groups' => ['sleep']]);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Request $request, Sleep $sleep): JsonResponse
    {
        $updatedSleep = $this->serializer->deserialize($request->getContent(), Sleep::class, 'json', ['object_to_populate' => $sleep]);
        $errors = $this->validator->validate($updatedSleep);

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors,
                'message' => [
                    'severity' => 'error',
                    'message' => 'Votre temps de sommeil n\'a pas pu être mis à jour :('
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->sleepRepository->save($updatedSleep, true);

        return $this->json([
            'sleep' => $updatedSleep,
            'sleeps' => $updatedSleep->getUser()->getSortedCollection('sleeps'),
            'message' => [
                'severity' => 'info',
                'message' => 'Votre temps de sommeil a été mis à jour avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['sleep']]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Sleep $sleep): JsonResponse
    {
        $this->sleepRepository->remove($sleep, true);
        return $this->json([
            'sleep' => $sleep,
            'sleeps' => $sleep->getUser()->getSortedCollection('sleeps'),
            'message' => [
                'severity' => 'info',
                'message' => 'Votre temps de sommeil a été supprimé avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['sleep']]);
    }
}
