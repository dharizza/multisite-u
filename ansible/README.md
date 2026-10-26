# Operación de la plataforma con Ansible (entorno del curso)

Playbooks para operar el multisite del curso sobre DDEV. Son los mismos
pasos que se hacen en producción; solo cambia el inventario.

## Requisitos

- El codebase del curso funcionando con DDEV (`ddev start`).
- Ansible: `pipx install ansible-core` (o `python3 -m pip install --user ansible-core`).
  En macOS también sirve `brew install ansible`. En Windows, dentro de WSL2.
- Esta carpeta `ansible/` copiada en la **raíz** del codebase (junto a `web/`).

## Primer uso

```bash
cd ansible
ansible-playbook playbooks/generar_inventario.yml   # crea el inventario a partir de sites.php
ansible-playbook playbooks/estado.yml               # revisa todos los subsitios
```

## Playbooks

| Playbook | Qué hace | Ejemplo |
|---|---|---|
| `generar_inventario.yml` | Crea `inventario/local/hosts.yml` (un host por subsitio) | `ansible-playbook playbooks/generar_inventario.yml` |
| `estado.yml` | Versión, base de datos, último cron, errores del informe de estado, portada | `ansible-playbook playbooks/estado.yml` |
| `desplegar.yml` | `composer install` una vez y `drush deploy` subsitio por subsitio, con modo mantenimiento y prueba de humo | `ansible-playbook playbooks/desplegar.yml --limit facultad_a` |
| `nuevo_subsitio.yml` | Base de datos, carpeta, settings.php, sites.php, hostname en DDEV, instalación e inventario | `ansible-playbook playbooks/nuevo_subsitio.yml -e nombre=facultad_c` |
| `respaldar.yml` | Base de datos y archivos de cada subsitio en `backups/<fecha>/`, conserva los últimos 7 | `ansible-playbook playbooks/respaldar.yml` |
| `restaurar.yml` | Restaura UN subsitio desde un respaldo y mide el tiempo | `ansible-playbook playbooks/restaurar.yml --limit facultad_a -e respaldo=2026-10-30_1015` |

## Opciones útiles

- `--limit <subsitio>`: solo ese subsitio (los nombres están en `inventario/local/hosts.yml`).
- `-e lote=2`: en `desplegar.yml`, actualiza de dos en dos (por defecto, de uno en uno).
- `--check`: muestra qué haría, sin hacerlo (los comandos de drush no se simulan).
- `-v`: más detalle de cada paso.

## Producción

`inventario/produccion-ejemplo/` muestra cómo se vería el inventario real:
los subsitios apuntan a un servidor por SSH, drush es `vendor/bin/drush` y
Composer instala sin dependencias de desarrollo. `nuevo_subsitio.yml` solo
funciona en local: en producción, además, hay que crear el vhost, el DNS y
el certificado, y las contraseñas vienen de Ansible Vault.

## Agregar al .gitignore del codebase

```
/backups/
/private/
```
