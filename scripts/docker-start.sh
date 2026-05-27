#!/bin/sh
cd "$(dirname "$0")/.."
echo "Demarrage EduSphere (Docker)..."
docker compose up --build -d
echo ""
echo "EduSphere est pret :"
echo "  Accueil    : http://localhost:8080"
echo "  Connexion  : http://localhost:8080/connexion"
echo ""
echo "Comptes demo : prof / prof123  ou  ali / ali123"
