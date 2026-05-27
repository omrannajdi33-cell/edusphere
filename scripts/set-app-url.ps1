param(
    [Parameter(Mandatory = $true, Position = 0)]
    [string]$Url
)

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$Url = $Url.Trim().TrimEnd("/")

if ($Url -notmatch '^https?://') {
    Write-Error "URL invalide. Exemple : https://edusphere-xxxx.onrender.com"
}

$lines = @(
    "# URL HTTPS de l'application Laravel (une ligne active, sans #)."
    "# Exemple : https://edusphere.example.com"
    $Url
)

Set-Content -Path (Join-Path $Root "production-url") -Value ($lines -join "`n") -Encoding UTF8
Write-Host "production-url mis a jour : $Url"
Write-Host ""
Write-Host "Prochaines etapes :"
Write-Host "  1. git add production-url"
Write-Host "  2. git commit -m 'Configure APP_URL pour la vitrine'"
Write-Host "  3. git push github main"
Write-Host ""
Write-Host "Ou relance GitHub Actions > Publish GitHub Pages avec app_url=$Url"
