<?php

/**
 * @file
 * settings.php del subsitio "prueba".
 *
 * GENERADO por playbooks/nuevo_subsitio.yml. En producción, los mismos
 * valores vendrían de variables cifradas con Ansible Vault.
 */

$databases['default']['default'] = [
  'database' => 'prueba',
  'username' => 'db',
  'password' => 'db',
  'host' => 'db',
  'port' => '3306',
  'driver' => 'mysql',
  'prefix' => '',
  'collation' => 'utf8mb4_general_ci',
];

$settings['hash_salt'] = 'CIsDA8HL9sugcV2yVwtxYT6u2egpoKfxZNAy18wvtOlUhmaTAEsuWbfmPAv3LFA0';

$settings['trusted_host_patterns'] = [
  '^prueba\.multisite\.ddev\.site$',
];

$settings['config_sync_directory'] = '../config/prueba';
$settings['file_private_path'] = '../private/prueba';

// Opciones locales opcionales (por ejemplo, las cabeceras de depuración).
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}
