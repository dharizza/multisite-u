<?php

declare(strict_types=1);

namespace Drupal\campus_greeting\EventSubscriber;

use Drupal\campus_greeting\VisitCounter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @todo Add description for this subscriber.
 */
final class CampusGreetingSubscriber implements EventSubscriberInterface {

  /**
   * Constructs a CampusGreetingSubscriber object.
   */
  public function __construct(
    private readonly VisitCounter $campusGreetingVisitCounter,
  ) {}

  /**
   * Kernel request event handler.
   */
  public function onRequest(RequestEvent $event): void {
    if (!$event->isMainRequest()) {
      return ;
    }

    $this->campusGreetingVisitCounter->increment();
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => ['onRequest'],
    ];
  }

}
