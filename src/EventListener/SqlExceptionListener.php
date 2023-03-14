<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class SqlExceptionListener
{
  public function onKernelException(ExceptionEvent $event)
  {
    $exception = $event->getThrowable();
    $response = new JsonResponse([
      'severity' => 'error',
      'message' => 'Les données sont invalides',
      'dev' => 'Erreur SQL ou Doctrine: ' . $exception->getMessage(),
    ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    $event->setResponse($response);
  }
}
