<?php

declare(strict_types=1);

namespace Drupal\campus_greeting;

use \Drupal\Core\State\StateInterface;

/**
 * Servicio que lleva la cuenta de visitas utilizando State API.
 */
class VisitCounter {

  public function __construct(
    protected StateInterface $state,
  ) {}

  public function increment(): void {
    $count = $this->getCount();
    $this->state->set('campus_greeting.visits', $count + 1);
  }

  public function getCount(): int {
    return (int) $this->state->get('campus_greeting.visits', 0);
  }
}
