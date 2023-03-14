<?php

namespace App\Controller;

use App\Entity\Drug;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DrugRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/drugs', name: 'app_user_drugs_')]
class DrugController extends AbstractController
{
    private SerializerInterface $serializer;
    private DrugRepository $drugRepository;
    private ValidatorInterface $validator;

    public function __construct(
        DrugRepository $drugRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->drugRepository = $drugRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {

        $drug = $this->serializer->deserialize($request->getContent(), Drug::class, 'json');

        $errors = $this->validator->validate($drug);

        $drug->setUser($this->getUser());

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors,
                'message' => [
                    'severity' => 'error',
                    'message' => 'Votre traitement médical n\'a pas pu être ajouté'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->drugRepository->save($drug, true);

        return $this->json([
            'drug' => $drug,
            'message' => [
                'severity' => 'info',
                'message' => 'Votre traitement médical a été enregistré avec succès'
            ]
        ], Response::HTTP_CREATED, [], ['groups' => ['drug']]);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Request $request, Drug $drug): JsonResponse
    {
        $updatedDrug = $this->serializer->deserialize($request->getContent(), Drug::class, 'json', ['object_to_populate' => $drug]);

        $errors = $this->validator->validate($updatedDrug);

        if (count($errors) > 0) {
            return $this->json([
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->drugRepository->save($updatedDrug, true);

        return $this->json([
            'drug' => $updatedDrug,
            'message' => [
                'severity' => 'info',
                'message' => 'Votre traitement médical a été mis à jour avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['drug']]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Drug $drug): JsonResponse
    {
        $drugId = $drug->getId();
        $this->drugRepository->remove($drug, true);

        return $this->json([
            'id' => $drugId,
            'message' => [
                'severity' => 'info',
                'message' => 'Votre traitement médical a été supprimé avec succès'
            ]
        ], Response::HTTP_OK, [], ['groups' => ['drug']]);
    }
}
