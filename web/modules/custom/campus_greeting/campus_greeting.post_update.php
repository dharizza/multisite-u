<?php

use Drupal\node\NodeInterface;

function _campus_greeting_strip_prefix(NodeInterface $node) {
  if (!$node->hasField('title')) {
    return FALSE;
  }

  $value = $node->get('title')->value;
  if (str_starts_with($value, 'UCR - ')) {
    $node->set('title', substr($value, strlen('UCR - ')));
    $node->save();
    \Drupal::logger('my_module')->error('nodo inicia con UCR');
    return TRUE;
  } else {
    \Drupal::logger('my_module')->error('no aplica cambios');
    return FALSE; // Ya se migró o no aplica.
  }
}

function campus_greeting_post_update_strip_event_prefix_5(array &$sandbox) {
  $storage = \Drupal::entityTypeManager()->getStorage('node');

  // Guardar ids la primera vez que se corre.
  if (!isset($sandbox['ids'])) {
    $sandbox['ids'] = $storage->getQuery()
      ->condition('type', 'event')
      ->accessCheck(FALSE)
      ->execute();

    $sandbox['total'] = count($sandbox['ids']);
    $sandbox['progress'] = 0;
    $sandbox['changed'] = 0;

    if ($sandbox['total'] === 0) {
      $sandbox['#finished'] = 1;
      return t('No hay eventos en este sitio.');
    }
  }

  $ids = array_splice($sandbox['ids'], 0, 2);
  foreach ($storage->loadMultiple($ids) as $node) {
    if (_campus_greeting_strip_prefix($node)) {
      $sandbox['changed']++;
    }
    $sandbox['progress']++;
  }

  $sandbox['#finished'] = empty($sandbox['ids']) ? 1 : $sandbox['progress'] / $sandbox['total'];

  if ($sandbox['#finished'] == 1) {
    return t(
      'Migración completa: @x nodos actualizados de @total revisados.',
      [
        '@x' => $sandbox['changed'],
        '@total' => $sandbox['total'],
      ]
    );
  }
}
