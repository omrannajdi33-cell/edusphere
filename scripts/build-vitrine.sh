#!/bin/sh
# Publie la vitrine statique (racine du repo) pour GitLab/GitHub Pages.
set -e

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="${VITRINE_OUT:-$ROOT/public}"

rm -rf "$OUT"
mkdir -p "$OUT/connexion"

cp "$ROOT/index.html" "$OUT/index.html"
cp "$ROOT/404.html" "$OUT/404.html"
cp "$ROOT/manifest.json" "$OUT/manifest.json"
cp "$ROOT/production-url" "$OUT/production-url" 2>/dev/null || true

if [ -f "$ROOT/public/favicon.ico" ]; then
  cp "$ROOT/public/favicon.ico" "$OUT/favicon.ico"
fi

set +e
APP_LINK="${PAGES_APP_URL:-$APP_URL}"
if [ -z "$APP_LINK" ] && [ -n "${DEPLOY_HOST:-}" ]; then
  case "$DEPLOY_HOST" in
    [0-9]*.[0-9]*) APP_LINK="http://${DEPLOY_HOST}" ;;
    *) APP_LINK="https://${DEPLOY_HOST}" ;;
  esac
fi
if [ -z "$APP_LINK" ] && [ -f "$ROOT/production-url" ]; then
  APP_LINK="$(awk '!/^#/ && !/^[[:space:]]*$/ { sub(/\/$/, ""); print; exit }' "$ROOT/production-url")"
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
  CONNEXION_URL="${APP_LINK}/connexion"
  cat > "$OUT/connexion/index.html" <<EOF
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="refresh" content="0;url=${CONNEXION_URL}" />
  <script>location.replace("${CONNEXION_URL}");</script>
  <title>Connexion — EduSphere</title>
</head>
<body><p><a href="${CONNEXION_URL}">Se connecter</a></p></body>
</html>
EOF
  echo "$APP_LINK" > "$OUT/production-url"
  echo "Connexion production: ${CONNEXION_URL}"
else
  cp "$ROOT/connexion/index.html" "$OUT/connexion/index.html"
fi

touch "$OUT/.nojekyll"
echo "Vitrine prête dans ${OUT#"$ROOT"/}"
