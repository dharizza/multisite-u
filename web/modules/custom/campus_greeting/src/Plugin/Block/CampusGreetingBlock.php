<?php

declare(strict_types=1);

namespace Drupal\campus_greeting\Plugin\Block;

use Drupal\campus_greeting\VisitCounter;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Bloque de saludo configurable con contador de visitas.
 */
#[Block(
  id: 'campus_greeting_block',
  admin_label: new TranslatableMarkup('Campus Greeting'),
)]
class CampusGreetingBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected VisitCounter $visitCounter,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('campus_greeting.visit_counter'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form['greeting_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mensaje de saludo'),
      '#default_value' => $this->configuration['greeting_message'] ?? 'Bienvenido al campus',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['greeting_message'] = $form_state->getValue('greeting_message');
  }

  public function build(): array {
    $message = $this->configuration['greeting_message'] ?? 'Bienvenido al campus';
    $count = $this->visitCounter->getCount();

    return [
      '#markup' => $this->t('@message — Visitantes hoy: @count', [
        '@message' => $message,
        '@count' => $count,
      ]),
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }
}
