<?php

namespace Drupal\campus_greeting\Drush\Commands;

use Drupal\campus_greeting\CampusGreetingPluginManager;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class CampusGreetingCommands extends DrushCommands {

  /**
   * Constructs a CampusGreetingCommands object.
   */
  public function __construct(
    private readonly CampusGreetingPluginManager $pluginManagerCampusGreeting,
  ) {
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.campus_greeting'),
    );
  }

  #[CLI\Command(name: 'campus-greeting:sources', aliases: ['cg-sources'])]
  #[CLI\Usage(name: 'campus-greeting:sources', description: 'Lista las fuentes de saludo registradas.')]
  public function listSources(): void {
    $sources = $this->pluginManagerCampusGreeting->getDefinitions();
    foreach ($sources as $id => $definition) {
      $this->io()->writeln(sprintf('%s: %s', $id, (string) $definition['label']));
    }
  }
}
