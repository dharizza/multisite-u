<?php

namespace Drupal\campus_greeting_feedback\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Formulario para que un visitante deje un saludo.
 */
class FeedbackForm extends FormBase {

  public function __construct(
    protected Connection $database,
    protected TimeInterface $time,
  ) {}

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('database'),
      $container->get('datetime.time'),
    );
  }

  public function getFormId(): string {
    return 'campus_greeting_feedback_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Su nombre'),
      '#required' => TRUE,
      '#maxlength' => 128,
    ];
    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Mensaje'),
      '#required' => TRUE,
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Enviar'),
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $name = $form_state->getValue('name');

    $this->database->insert('campus_greeting_feedback')
      ->fields([
        'name' => $name,
        'message' => $form_state->getValue('message'),
        'created' => $this->time->getRequestTime(),
      ])
      ->execute();
    Cache::invalidateTags(['campus_greeting_feedback_list']);

    $this->messenger()->addStatus($this->t('¡Gracias, ' . $name . '! Su saludo fue publicado.'));
    $form_state->setRedirect('campus_greeting_feedback.list');
  }

}
