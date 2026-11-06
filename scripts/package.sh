#!/usr/bin/env bash
# Build the submission ZIP:  scripts/package.sh "Your Full Name"
# - copies the project into a folder named after you
# - excludes vendor/, node_modules/, .git, logs, caches and the dev .env
# - ships a ready-to-use .env (same as .env.example + APP_KEY)
set -euo pipefail

NAME="${1:-}"
if [[ -z "$NAME" ]]; then
    echo "Usage: $0 \"Your Full Name\"" >&2
    exit 1
fi

PROJECT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
FOLDER_NAME="$(echo "$NAME" | tr ' ' '_' | tr -cd '[:alnum:]_-')"
OUT_DIR="$(dirname "$PROJECT_DIR")"
STAGE="$(mktemp -d)"
DEST="$STAGE/$FOLDER_NAME"

rsync -a "$PROJECT_DIR/" "$DEST/" \
    --exclude vendor --exclude node_modules --exclude .git \
    --exclude 'storage/logs/*.log' \
    --exclude 'storage/framework/cache/data/*' --exclude 'storage/framework/sessions/*' \
    --exclude 'storage/framework/views/*' --exclude 'storage/framework/testing' \
    --exclude 'bootstrap/cache/*.php' --exclude .env --exclude public/storage \
    --exclude '*.zip' --exclude .phpunit.result.cache

# Ready-to-run .env for the reviewer (local MySQL defaults + generated APP_KEY)
cp "$DEST/.env.example" "$DEST/.env"
APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" "$DEST/.env"

ZIP_PATH="$OUT_DIR/$FOLDER_NAME.zip"
rm -f "$ZIP_PATH"
(cd "$STAGE" && zip -qr "$ZIP_PATH" "$FOLDER_NAME")
rm -rf "$STAGE"

echo "Created: $ZIP_PATH ($(du -h "$ZIP_PATH" | cut -f1))"
