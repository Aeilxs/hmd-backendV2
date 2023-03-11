<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class ExceptionListener
{
  public function onKernelException(ExceptionEvent $event)
  {
    $exception = $event->getThrowable();

    if ($exception instanceof UniqueConstraintViolationException) {
      $response = new JsonResponse([
        'error' => [
          'severity' => 'error',
          'message' => 'Email déjà utilisé'
        ]
      ], Response::HTTP_BAD_REQUEST);
      $event->setResponse($response);
    }
  }
}
