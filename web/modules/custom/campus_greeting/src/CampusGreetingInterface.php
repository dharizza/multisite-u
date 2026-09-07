<?php

declare(strict_types=1);

namespace Drupal\campus_greeting;

/**
 * Interfaz para cualquier fuente de mensajes de saludos.
 */
interface CampusGreetingInterface {

  /**
   * Genera el mensaje de esta fuente.
   */
  public function getMessage(): string;

}
