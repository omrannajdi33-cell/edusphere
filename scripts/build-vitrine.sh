#!/bin/sh
# Assemble la vitrine GitLab Pages dans ./public (artefact CI uniquement).
set -e

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SRC="$ROOT/site/vitrine"
OUT="$ROOT/public"
FAVICON=""
if [ -f "$OUT/favicon.ico" ]; then
  FAVICON="$OUT/favicon.ico"
fi

rm -rf "$OUT"
mkdir -p "$OUT/connexion"

set +e
APP_LINK="${PAGES_APP_URL:-$APP_URL}"
if [ -z "$APP_LINK" ] && [ -n "${DEPLOY_HOST:-}" ]; then
  case "$DEPLOY_HOST" in
    [0-9]*.[0-9]*) APP_LINK="http://${DEPLOY_HOST}" ;;
    *) APP_LINK="https://${DEPLOY_HOST}" ;;
  esac
fi
if [ -z "$APP_LINK" ] && [ -f "$SRC/production-url" ]; then
  APP_LINK="$(awk '!/^#/ && !/^[[:space:]]*$/ { sub(/\/$/, ""); print; exit }' "$SRC/production-url")"
fi
APP_LINK="${APP_LINK%/}"
if [ -n "$APP_LINK" ] && echo "$APP_LINK" | grep -qE '127\.0\.0\.1|localhost'; then
  echo "WARNING: APP_URL locale ignorée."
  APP_LINK=""
fi
if [ -n "$APP_LINK" ] && echo "$APP_LINK" | grep -qiE 'gitlab\.io|github\.io'; then
  echo "WARNING: APP_URL ne peut pas être une URL Pages (gitlab.io / github.io)."
  APP_LINK=""
fi
set -e

if [ -n "$APP_LINK" ]; then
  CONNEXION_URL="${APP_LINK}/connexion"
  cp "$SRC/connexion/index.html" "$OUT/connexion/index.html"
  sed -i "s|__LOGIN_URL__|${CONNEXION_URL}|g" "$OUT/connexion/index.html"
  echo "Connexion production: ${CONNEXION_URL}"
else
  CONNEXION_URL="connexion/"
  cp "$SRC/connexion/pending.html" "$OUT/connexion/index.html"
  echo "WARNING: APP_URL absente — connexion en attente sur la vitrine."
fi

cp "$SRC/index.html" "$OUT/index.html"
cp "$SRC/presentation.html" "$OUT/presentation.html"
cp "$SRC/404.html" "$OUT/404.html"
cp "$SRC/manifest.json" "$OUT/manifest.json"
if [ -n "$FAVICON" ] && [ -f "$FAVICON" ]; then
  cp "$FAVICON" "$OUT/favicon.ico"
fi

sed -i "s|__CONNEXION_URL__|${CONNEXION_URL}|g" "$OUT/index.html"

touch "$OUT/.nojekyll"

echo "Vitrine prête dans public/"
ls -la "$OUT" "$OUT/connexion"
