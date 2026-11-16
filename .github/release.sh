#!/usr/bin/env bash
# Упаковывает собранный проект в архив релиза и проверяет, что из него ставится сайт.
# Запуск из корня проекта после composer install --no-dev и npm run build:
#   .github/release.sh v1.0.0 [папка-для-архива]

set -euo pipefail

TAG=${1:?Укажите тег, например v1.0.0}
OUT=${2:-dist}
NAME="motor-$TAG"

rm -rf "$OUT/motor" "$OUT/$NAME.zip"
mkdir -p "$OUT/motor"

rsync -a ./ "$OUT/motor/" \
    --include='/storage/*/.gitignore' \
    --exclude='/storage/*/*' \
    --exclude='/.git' \
    --exclude='/.github' \
    --exclude='/.superpowers' \
    --exclude='/.idea' \
    --exclude='/node_modules' \
    --exclude='/docs' \
    --exclude='/dist' \
    --exclude='/resources/css' \
    --exclude='/resources/js' \
    --exclude='/package.json' \
    --exclude='/package-lock.json' \
    --exclude='/vite.config.js' \
    --exclude='/deploy.yaml' \
    --exclude='/public/hot'

for dir in storage/database storage/backups storage/logs storage/cache; do
    mkdir -p "$OUT/motor/$dir"
    [ -f "$OUT/motor/$dir/.gitignore" ] || printf '*\n!.gitignore\n' > "$OUT/motor/$dir/.gitignore"
done

for required in vendor/autoload.php public/build/manifest.json public/index.php motor; do
    [ -e "$OUT/motor/$required" ] || { echo "В архиве нет $required" >&2; exit 1; }
done

if find "$OUT/motor/storage" -type f ! -name '.gitignore' | grep -q .; then
    echo "В архив попали данные сайта:" >&2
    find "$OUT/motor/storage" -type f ! -name '.gitignore' >&2
    exit 1
fi

(cd "$OUT" && zip -qr "$NAME.zip" motor)

CHECK=$(mktemp -d)
trap '[ -n "${SERVER:-}" ] && kill "$SERVER" 2>/dev/null; rm -rf "$CHECK"' EXIT
unzip -q "$OUT/$NAME.zip" -d "$CHECK"

PORT=8765
php -S 127.0.0.1:$PORT -t "$CHECK/motor/public" "$CHECK/motor/public/index.php" >"$CHECK/server.log" 2>&1 &
SERVER=$!
disown
for _ in $(seq 1 50); do
    curl -s -o /dev/null "http://127.0.0.1:$PORT/robots.txt" && break
    sleep 0.2
done

first=$(curl -s "http://127.0.0.1:$PORT/")
echo "$first" | grep -q 'Установка завершена' || { echo "Первый запрос не установил сайт" >&2; echo "$first" | head -20 >&2; exit 1; }

code=$(curl -s -o "$CHECK/second.html" -w '%{http_code}' "http://127.0.0.1:$PORT/")
if [ "$code" != 200 ] || grep -qE 'Fatal error|Uncaught' "$CHECK/second.html"; then
    echo "Второй запрос: код $code" >&2
    head -20 "$CHECK/second.html" >&2
    exit 1
fi

echo "$OUT/$NAME.zip: $(du -h "$OUT/$NAME.zip" | cut -f1), установка проверена"
