<?php

namespace Drupal\campus_greeting_feedback\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Lista, busca y elimina saludos del campus.
 */
class FeedbackController extends ControllerBase {

  public function __construct(protected Connection $database) {}

  public static function create(ContainerInterface $container): static {
    return new static($container->get('database'));
  }

  /**
   * Muestra los últimos 50 saludos.
   */
  public function list(): array {
    $items = $this->database->select('campus_greeting_feedback', 'f')
      ->fields('f', ['id', 'name', 'message', 'created'])
      ->orderBy('created', 'DESC')
      ->range(0, 50)
      ->execute()
      ->fetchAll();

    return [
      '#theme' => 'campus_greeting_feedback_list',
      '#items' => $items,
      '#can_delete' => TRUE,
      '#cache' => [
        'tags' => ['campus_greeting_feedback_list'],
      ],
    ];
  }

  /**
   * Busca saludos por nombre: /saludos/buscar?q=...
   */
  public function search(Request $request): array {
    $term = (string) $request->query->get('q', '');

    // Hallazgo 1: Query builder + escapeLike() en lugar de concatenar SQL.
    $items = $this->database->select('campus_greeting_feedback', 'f')
      ->fields('f', ['id', 'name', 'message', 'created'])
      ->condition('name', '%' . $this->database->escapeLike($term) . '%', 'LIKE')
      ->orderBy('created', 'DESC')
      ->range(0, 50)
      ->execute()
      ->fetchAll();

    return [
      '#theme' => 'campus_greeting_feedback_list',
      '#items' => $items,
      '#can_delete' => $this->currentUser()->hasPermission('administer campus greetings'),
      '#cache' => [
        'tags' => ['campus_greeting_feedback_list'],
        'contexts' => ['url.query_args:q', 'user.permissions'],
      ],
    ];
  }

  /**
   * Elimina un saludo.
   */
  public function delete(int $id): RedirectResponse {
    $this->database->delete('campus_greeting_feedback')
      ->condition('id', $id)
      ->execute();
    Cache::invalidateTags(['campus_greeting_feedback_list']);
    $this->messenger()->addStatus($this->t('Saludo eliminado.'));
    return $this->redirect('campus_greeting_feedback.list');
  }

}
