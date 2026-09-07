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
  id: 'time_of_day',
  label: new TranslatableMarkup('Hora del día'),
  description: new TranslatableMarkup('Saludo cambia según hora del día.'),
)]
final class TimeOfDayGreeting extends CampusGreetingPluginBase {

  #[Override]
  public function getMessage(): string {
    $hour = (int) date('G');

    if ($hour < 12) {
      return "Buenos días, campus";
    } else if ($hour < 19) {
      return "Buenos tardes, campus";
    }
    return "Buenas noches, campus";
  }

}
