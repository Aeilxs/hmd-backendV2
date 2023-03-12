<?php

namespace App\EventListener;

use Doctrine\DBAL\Exception\DriverException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class SqlExceptionListener
{
  public function onKernelException(ExceptionEvent $event)
  {
    $exception = $event->getThrowable();

    if ($exception instanceof DriverException) {
      $response = new JsonResponse([
        'severity' => 'error',
        'message' => 'Les données sont invalides',
        'dev' => 'Erreur SQL : ' . $exception->getMessage(),
      ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
      $event->setResponse($response);
    }
  }
}
