#!/bin/sh
# Peluncur msdm (Laravel) untuk Mac dan Linux.
# Pemakaian: ./mulai.sh          (hanya di komputer ini)
#            ./mulai.sh --lan    (bisa dibuka dari perangkat lain di jaringan yang sama)
cd "$(dirname "$0")" || exit 1
HOSTADDR="127.0.0.1"
[ "$1" = "--lan" ] && HOSTADDR="0.0.0.0"
PORT="${PORT:-8000}"

if ! command -v php >/dev/null 2>&1; then
  echo "PHP belum terpasang. Pasang PHP 8.2 atau lebih baru (lihat PANDUAN-LARAVEL.md), lalu jalankan lagi."
  exit 1
fi
if [ ! -d vendor ]; then
  if ! command -v composer >/dev/null 2>&1; then
    echo "Composer belum terpasang. Lihat https://getcomposer.org lalu jalankan lagi."
    exit 1
  fi
  echo "Memasang kebutuhan (pertama kali), mohon tunggu..."
  composer install --no-dev --optimize-autoloader || exit 1
fi
[ -f .env ] || cp .env.example .env

( sleep 3; if command -v open >/dev/null 2>&1; then open "http://localhost:$PORT"; elif command -v xdg-open >/dev/null 2>&1; then xdg-open "http://localhost:$PORT"; fi ) >/dev/null 2>&1 &

# Beberapa proses agar beberapa kandidat bisa mengerjakan tes bersamaan (Mac/Linux)
export PHP_CLI_SERVER_WORKERS="${PHP_CLI_SERVER_WORKERS:-4}"
exec php artisan serve --host="$HOSTADDR" --port="$PORT"
