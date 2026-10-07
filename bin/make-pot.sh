#!/usr/bin/env bash
set -euo pipefail

if ! command -v wp >/dev/null 2>&1; then
  echo "WP-CLI gerekli: https://wp-cli.org/"
  exit 1
fi

mkdir -p languages
wp i18n make-pot . languages/asosyoloji.pot \
  --domain=asosyoloji \
  --exclude=vendor,node_modules,build,.git,.github

echo "languages/asosyoloji.pot güncellendi."
