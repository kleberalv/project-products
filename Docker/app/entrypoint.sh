#!/bin/sh
set -e

echo "[entrypoint] Iniciando setup da aplicação..."
cd /var/www

# Aguardar banco disponível
echo "[entrypoint] Aguardando MariaDB em db:3306..."
until php -r "exit((@fsockopen('db', 3306)) ? 0 : 1);"; do
  sleep 1
done

echo "[entrypoint] Banco disponível. Prosseguindo para migrations."

# Instalar dependências composer (se necessário)
if [ ! -d vendor ]; then
  echo "[entrypoint] Executando composer install..."
  composer install --no-interaction --prefer-dist
else
  echo "[entrypoint] vendor já existe, pulando composer install."
fi

# Gerar chave da aplicação (idempotente)
echo "[entrypoint] Gerando APP_KEY (se necessário)..."
php artisan key:generate || true

# Rodar migrations com retries (aguarda inicialização completa do DB)
echo "[entrypoint] Executando migrations..."
ATTEMPTS=0
until php artisan migrate --force; do
  ATTEMPTS=$((ATTEMPTS+1))
  if [ "$ATTEMPTS" -ge 30 ]; then
    echo "[entrypoint] Falha ao executar migrations após várias tentativas." >&2
    exit 1
  fi
  echo "[entrypoint] Migrations ainda não aplicadas. Tentando novamente... ($ATTEMPTS)"
  sleep 2
done

# Rodar seeders
echo "[entrypoint] Executando seeders..."
php artisan db:seed --force || true

# Opcional: criar storage link
if [ ! -e public/storage ]; then
  echo "[entrypoint] Criando storage:link..."
  php artisan storage:link || true
fi

echo "[entrypoint] Setup concluído. Iniciando php-fpm..."
php-fpm
