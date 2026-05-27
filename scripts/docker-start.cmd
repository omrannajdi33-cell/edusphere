@echo off
setlocal
cd /d "%~dp0.."
echo Demarrage EduSphere (Docker)...
docker compose up --build -d
if errorlevel 1 exit /b 1
echo.
echo EduSphere est pret :
echo   Accueil    : http://localhost:8080
echo   Connexion  : http://localhost:8080/connexion
echo.
echo Comptes demo : prof / prof123  ou  ali / ali123
echo.
echo Arreter : docker compose down
