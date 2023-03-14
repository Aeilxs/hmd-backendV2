<?php

namespace App\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class TimestampSubscriber implements EventSubscriberInterface
{
  public function getSubscribedEvents(): array
  {
    return [Events::prePersist, Events::preUpdate];
  }

  public function prePersist(LifecycleEventArgs $args): void
  {
    $entity = $args->getObject();
    if (!method_exists($entity, 'setCreatedAt')) return;
    $entity->setCreatedAt(new \DateTimeImmutable());
  }

  public function preUpdate(LifecycleEventArgs $args): void
  {
    $entity = $args->getObject();
    if (!method_exists($entity, 'setUpdatedAt')) return;
    $entity->setUpdatedAt(new \DateTimeImmutable());
  }
}
