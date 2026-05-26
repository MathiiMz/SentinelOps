# SentinelOps

Plataforma de gestión de incidentes SOC (Security Operations Center) con Laravel 10: panel web en Blade y API REST con Sanctum.

## Requisitos

- PHP 8.1+ con extensión `pdo_sqlite` o `pdo_mysql`
- Composer

En Arch/CachyOS:

```bash
sudo pacman -S php-sqlite   # o php-pdo php-mysql
```

## Instalación

```bash
composer install
cp .env.example .env
touch database/database.sqlite   # si usas SQLite
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

O ejecuta `./setup.sh`.

Panel web: http://localhost:8000/login (usa siempre `localhost`, no mezcles con `127.0.0.1`)  
API: http://localhost:8000/api

Si ves **419 Page Expired** al iniciar sesión: `php artisan config:clear` y entra solo por la misma URL (p. ej. `http://localhost:8000`).

## Credenciales de prueba

| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | admin@sentinelops.com | AdminPassword123! |
| Analyst | john@sentinelops.com | AnalystPassword123! |
| Viewer | manager@sentinelops.com | ViewerPassword123! |

## Roles

- **admin** — Todo, incluye gestión de usuarios (`/users`)
- **analyst** — Crear y gestionar incidentes
- **viewer** — Solo lectura de incidentes

## API (resumen)

Autenticación pública: `POST /api/auth/register`, `POST /api/auth/login`  
Con token Bearer: incidentes, comentarios, usuarios (admin). Ver rutas con `php artisan route:list`.
