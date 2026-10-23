<?php

namespace Drupal\campus_greeting_widgets\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
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
class LatestGreetingsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected Connection $database,
    protected AccountInterface $currentUser,
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
      $container->get('current_user'),
      $container->get('extension.list.module'),
      $container->get('file_url_generator'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    // Total de saludos recibidos.
    $all = $this->database->select('campus_greeting_feedback', 'f')
      ->fields('f')
      ->execute()
      ->fetchAll();
    $total = count($all);

    // Últimos 5 saludos.
    $items = $this->database->select('campus_greeting_feedback', 'f')
      ->fields('f', ['id', 'name', 'message', 'created'])
      ->orderBy('created', 'DESC')
      ->range(0, 5)
      ->execute()
      ->fetchAll();

    $icon_path = $this->moduleList->getPath('campus_greeting_widgets') . '/images/saludo.svg';

    return [
      '#theme' => 'campus_greeting_widgets_latest',
      '#welcome' => $this->t('Hola, @name', ['@name' => $this->currentUser->getDisplayName()]),
      '#items' => $items,
      '#total' => $total,
      '#icon_url' => $this->fileUrlGenerator->generateString($icon_path),
      // El bloque no se actualizaba cuando alguien enviaba un saludo nuevo,
      // así que se desactivó la caché.
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

}
