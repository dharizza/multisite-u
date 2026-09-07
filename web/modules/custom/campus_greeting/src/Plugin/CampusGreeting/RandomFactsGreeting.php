<?php

declare(strict_types=1);

namespace Drupal\campus_greeting\Plugin\CampusGreeting;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\campus_greeting\Attribute\CampusGreeting;
use Drupal\campus_greeting\CampusGreetingPluginBase;
use Override;

/**
 * Plugin implementation of the campus_greeting.
 */
#[CampusGreeting(
  id: 'random_fact',
  label: new TranslatableMarkup('Dato curioso'),
  description: new TranslatableMarkup('Saluda dando un dato curioso aleatorio.'),
)]
final class RandomFactsGreeting extends CampusGreetingPluginBase {

  protected const FACTS = [
    "La universidad tiene más de 85 años de historia",
    "La biblioteca abre todos los días de la semana",
    "Hay más de 100 grupos estudiantiles activos este semestre",
  ];

  #[Override]
  public function getMessage(): string {
    $index = array_rand(self::FACTS);
    return self::FACTS[$index];
  }

}
