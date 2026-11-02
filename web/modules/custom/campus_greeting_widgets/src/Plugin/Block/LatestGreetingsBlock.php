<?php

namespace Drupal\campus_greeting_widgets\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Muestra un saludo personal, el total y los últimos 5 saludos del campus.
 */
#[Block(
  id: 'campus_greeting_widgets_latest',
  admin_label: new TranslatableMarkup('Últimos saludos del campus'),
  category: new TranslatableMarkup('UCR Training'),
)]
class LatestGreetingsBlock extends BlockBase implements ContainerFactoryPluginInterface, TrustedCallbackInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected Connection $database,
    protected ModuleExtensionList $moduleList,
    protected FileUrlGeneratorInterface $fileUrlGenerator,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
      $container->get('extension.list.module'),
      $container->get('file_url_generator'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    // Hallazgo P4: contar en la base de datos, no cargando todas las filas.
    $total = (int) $this->database->select('campus_greeting_feedback', 'f')
      ->countQuery()
      ->execute()
      ->fetchField();

    $items = $this->database->select('campus_greeting_feedback', 'f')
      ->fields('f', ['id', 'name', 'message', 'created'])
      ->orderBy('created', 'DESC')
      ->range(0, 5)
      ->execute()
      ->fetchAll();

    $icon_path = $this->moduleList->getPath('campus_greeting_widgets') . '/images/saludo.svg';

    return [
      '#theme' => 'campus_greeting_widgets_latest',
      // Hallazgo P3: la parte personal va en un placeholder (lazy builder).
      // Así la lista se guarda en caché una sola vez para todos, y solo el
      // saludo personal se calcula por usuario (BigPipe lo envía después).
      '#welcome' => [
        '#lazy_builder' => [static::class . '::buildWelcome', []],
        '#create_placeholder' => TRUE,
      ],
      '#items' => $items,
      '#total' => $total,
      '#icon_url' => $this->fileUrlGenerator->generateString($icon_path),
      // Hallazgos P1 y P2: sin max-age 0. El tag se invalida en
      // FeedbackForm::submitForm() cada vez que llega un saludo nuevo.
      '#cache' => [
        'tags' => ['campus_greeting_feedback_list'],
      ],
    ];
  }

  /**
   * Lazy builder: saludo personal para el usuario actual.
   */
  public static function buildWelcome(): array {
    return [
      '#markup' => new TranslatableMarkup('Hola, @name', [
        '@name' => \Drupal::currentUser()->getDisplayName(),
      ]),
      '#cache' => [
        'contexts' => ['user'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return ['buildWelcome'];
  }

}
