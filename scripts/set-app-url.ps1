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

$content = @(
    "# URL HTTPS de l'application Laravel (une ligne active, sans #)."
    "# Exemple : https://edusphere.example.com"
    $Url
) -join "`n"

$path = Join-Path $Root "production-url"
$utf8NoBom = New-Object System.Text.UTF8Encoding $false
[System.IO.File]::WriteAllText($path, $content + "`n", $utf8NoBom)

Write-Host "production-url mis a jour : $Url"
