#!/bin/sh
set -e

if [ -z "${1:-}" ]; then
  echo "Usage: $0 https://edusphere-xxxx.onrender.com"
  exit 1
fi

URL="${1%/}"
ROOT="$(cd "$(dirname "$0")/.." && pwd)"

case "$URL" in
  http://*|https://*) ;;
  *) echo "URL invalide: $URL"; exit 1 ;;
esac

cat > "$ROOT/production-url" <<EOF
# URL HTTPS de l'application Laravel (une ligne active, sans #).
# Exemple : https://edusphere.example.com
$URL
EOF

echo "production-url mis a jour : $URL"
echo "Pousse sur main ou relance le workflow Pages avec app_url=$URL"
