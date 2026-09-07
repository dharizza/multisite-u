<?php

declare(strict_types=1);

namespace Drupal\campus_greeting\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\campus_greeting\CampusGreetingPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for Campus Greeting routes.
 */
final class GreetingSourcesController extends ControllerBase {

  /**
   * The controller constructor.
   */
  public function __construct(
    private readonly CampusGreetingPluginManager $campusGreetingManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('plugin.manager.campus_greeting'),
    );
  }

  /**
   * Builds the response.
   */
  public function list(): JsonResponse {
    $data = [];
    $sources = $this->campusGreetingManager->getDefinitions();
    foreach ($sources as $id => $definition) {
      $data[] = [
        'id' => $id,
        'label' => (string) $definition['label'],
        'message' => $this->campusGreetingManager->createInstance($id)->getMessage(),
      ];
    }

    return new JsonResponse($data);
  }

}
