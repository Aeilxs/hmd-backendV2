<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
  public function onKernelException(ExceptionEvent $event)
  {
    $exception = $event->getThrowable();
    $class = (new \ReflectionClass($exception))->getShortName();
    switch ($class) {
      case 'NotFoundHttpException':
        $response = $this->createResponse(
          "Il n'y a aucune ressource à l'adresse demandée.",
          JsonResponse::HTTP_NOT_FOUND
        );
        break;

      case 'NotNormalizableValueException':
        $response = $this->createResponse(
          "Les données que vous avez fournis sont invalides.",
          JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        );
        break;

      case 'MethodNotAllowedHttpException':
        $response = $this->createResponse(
          "Le verbe HTTP n'est pas supporté.",
          JsonResponse::HTTP_METHOD_NOT_ALLOWED
        );
        break;

      case 'BadRequestHttpException':
        $response = $this->createResponse(
          "La requête est mal formée.",
          JsonResponse::HTTP_BAD_REQUEST
        );
        break;

      default:
        return;
    }

    $event->setResponse($response);
  }

  private function createResponse(string $message, int $code = 500): JsonResponse
  {
    $response = new JsonResponse([
      'message' => [
        'severity' => 'error',
        'message' => $message
      ]
    ], $code);
    return $response;
  }
}
