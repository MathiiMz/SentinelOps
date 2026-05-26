#!/usr/bin/env bash
set -euo pipefail

echo "Inicializando SentinelOps..."

if ! command -v composer &> /dev/null; then
    echo "Composer no está instalado. Instálalo desde https://getcomposer.org"
    exit 1
fi

if ! php -m | grep -qi pdo_sqlite && ! php -m | grep -qi pdo_mysql; then
    echo "Falta el driver PDO para base de datos."
    echo "En Arch/CachyOS ejecuta: sudo pacman -S php-sqlite"
    echo "O para MySQL: sudo pacman -S php-pdo php-mysql"
    exit 1
fi

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ ! -f database/database.sqlite ] && grep -q '^DB_CONNECTION=sqlite' .env 2>/dev/null; then
    touch database/database.sqlite
fi

composer install
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force

echo ""
echo "Listo. Inicia el servidor con:"
echo "  php artisan serve"
echo ""
echo "Web:    http://localhost:8000/login"
echo "Admin:  admin@sentinelops.com / AdminPassword123!"
echo "Panel usuarios (admin): /users"
