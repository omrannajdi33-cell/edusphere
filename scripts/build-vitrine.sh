#!/bin/sh
# Publie la vitrine HTML (racine du repo) pour GitLab/GitHub Pages.
set -e

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="${VITRINE_OUT:-$ROOT/public}"

rm -rf "$OUT"
mkdir -p "$OUT/assets"

cp "$ROOT/index.html" "$OUT/index.html"
cp "$ROOT/404.html" "$OUT/404.html"
cp "$ROOT/manifest.json" "$OUT/manifest.json"
cp "$ROOT/assets/edu.css" "$OUT/assets/edu.css"
cp "$ROOT/connexion.html" "$OUT/connexion.html"

if [ -f "$ROOT/public/favicon.ico" ]; then
  cp "$ROOT/public/favicon.ico" "$OUT/favicon.ico"
fi

read_app_link() {
  if [ -f "$ROOT/production-url" ]; then
    grep -E '^https?://' "$ROOT/production-url" | head -1 | tr -d '\r' | sed 's/^[[:space:]]*//;s/[[:space:]]*$//;s/\xEF\xBB\xBF//'
  fi
}

set +e
APP_LINK="${PAGES_APP_URL:-$APP_URL}"
if [ -z "$APP_LINK" ] && [ -n "${DEPLOY_HOST:-}" ]; then
  case "$DEPLOY_HOST" in
    [0-9]*.[0-9]*) APP_LINK="http://${DEPLOY_HOST}" ;;
    *) APP_LINK="https://${DEPLOY_HOST}" ;;
  esac
fi
if [ -z "$APP_LINK" ]; then
  APP_LINK="$(read_app_link)"
fi
APP_LINK="${APP_LINK%/}"
if [ -n "$APP_LINK" ] && echo "$APP_LINK" | grep -qE '127\.0\.0\.1|localhost'; then
  APP_LINK=""
fi
if [ -n "$APP_LINK" ] && echo "$APP_LINK" | grep -qiE 'gitlab\.io|github\.io'; then
  APP_LINK=""
fi
set -e

if [ -n "$APP_LINK" ] && echo "$APP_LINK" | grep -qE '^https?://'; then
  printf '%s\n' "$APP_LINK" > "$OUT/production-url"
  sed "s|__APP_URL__|${APP_LINK}|g" "$ROOT/assets/vitrine.js" > "$OUT/assets/vitrine.js"
  sed "s|href=\"connexion.html\"|href=\"${APP_LINK}/connexion\"|g" "$OUT/index.html" > "$OUT/index.html.tmp" && mv "$OUT/index.html.tmp" "$OUT/index.html"
  echo "Connexion production: ${APP_LINK}/connexion"
else
  cp "$ROOT/production-url" "$OUT/production-url" 2>/dev/null || true
  sed "s|__APP_URL__||g" "$ROOT/assets/vitrine.js" > "$OUT/assets/vitrine.js"
  echo "WARNING: APP_URL absente — configure production-url ou vars.APP_URL (Render)"
fi

touch "$OUT/.nojekyll"
echo "Vitrine prête dans ${OUT#"$ROOT"/}"
