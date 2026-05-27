@echo off
setlocal
cd /d "%~dp0.."
echo Demarrage EduSphere PRODUCTION (Docker)...
docker compose -f docker-compose.prod.yml up --build -d
if errorlevel 1 exit /b 1
echo.
echo EduSphere production locale :
echo   Accueil    : http://localhost:8080
echo   Connexion  : http://localhost:8080/connexion
echo   Sante      : http://localhost:8080/up
echo.
echo Deploy Render : push sur GitHub puis Blueprint render.yaml
echo Arreter : docker compose -f docker-compose.prod.yml down
