<?php

declare(strict_types=1);

namespace Drupal\campus_greeting;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\campus_greeting\Attribute\CampusGreeting;

/**
 * CampusGreeting plugin manager.
 */
final class CampusGreetingPluginManager extends DefaultPluginManager {

  /**
   * Constructs the object.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/CampusGreeting', $namespaces, $module_handler, CampusGreetingInterface::class, CampusGreeting::class);
    $this->alterInfo('campus_greeting_info');
    $this->setCacheBackend($cache_backend, 'campus_greeting_plugins');
  }

}
