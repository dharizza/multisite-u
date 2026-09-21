<?php

function campus_greeting_deploy_warm_greeting_cache() {
  $manager = \Drupal::service('plugin.manager.campus_greeting');
  $manager->clearCachedDefinitions();
  $definitions = $manager->getDefinitions();

  \Drupal::logger('campus_greeting')->notice('Precalentó @x definiciones de GreetingSources', ['@x' => count($definitions)]);

  return t('Cache de GreetingSource recalendatdo (@x definiciones)', ['@x' => count($definitions)]);
}
