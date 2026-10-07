#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BUILD_DIR="$ROOT/build"
THEME_DIR="$BUILD_DIR/asosyoloji"
ZIP_PATH="$BUILD_DIR/asosyoloji-theme.zip"

rm -rf "$THEME_DIR" "$ZIP_PATH"
mkdir -p "$THEME_DIR"

rsync -a "$ROOT/" "$THEME_DIR/" \
  --exclude '.git' \
  --exclude '.github' \
  --exclude 'build' \
  --exclude '.gitignore' \
  --exclude 'composer.json' \
  --exclude 'composer.lock' \
  --exclude 'phpcs.xml.dist' \
  --exclude 'bin' \
  --exclude 'vendor' \
  --exclude 'node_modules'

(
  cd "$BUILD_DIR"
  zip -qr "$(basename "$ZIP_PATH")" asosyoloji
)

unzip -t "$ZIP_PATH" >/dev/null
echo "$ZIP_PATH"
