<?php

declare(strict_types=1);

namespace Drupal\campus_greeting;

use Drupal\Component\Plugin\PluginBase;

/**
 * Base class for campus_greeting plugins.
 */
abstract class CampusGreetingPluginBase extends PluginBase implements CampusGreetingInterface {

  /**
   * {@inheritdoc}
   */
  public function getMessage(): string {
    // Cast the label to a string since it is a TranslatableMarkup object.
    return (string) $this->pluginDefinition['label'];
  }

}
