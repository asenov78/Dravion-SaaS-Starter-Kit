param([string]$Version = "")

$src = $PSScriptRoot

# Auto-detect version from config if not passed
if (-not $Version) {
    $Version = (Select-String -Path "$src\config\dravion.php" -Pattern "'version'\s*=>\s*'([^']+)'" | `
        ForEach-Object { $_.Matches[0].Groups[1].Value } | Select-Object -First 1)
}

$out  = Join-Path (Split-Path $src) "dravion-v${Version}-full.zip"
$tmp  = Join-Path $env:TEMP "dravion-build-$(Get-Date -Format 'yyyyMMddHHmmss')"

Write-Host "Version : $Version"
Write-Host "Temp dir: $tmp"
Write-Host "Output  : $out"

# 1. Robocopy to temp (handles locked files better than Get-ChildItem)
$excludeDirs  = @('.git', 'node_modules', 'releases', '.claude')
$excludeFiles = @('.env', '*.log', 'make-full-zip.ps1', '.phpunit.result.cache', 'phpunit.xml')

robocopy $src $tmp /E /XD $excludeDirs /XF $excludeFiles /NFL /NDL /NJH /NJS /NC /NS | Out-Null

# 2. Remove leftover storage data (keep skeleton dirs)
@(
    "$tmp\storage\logs",
    "$tmp\storage\app\public",
    "$tmp\storage\framework\cache\data",
    "$tmp\storage\framework\sessions",
    "$tmp\storage\framework\views",
    "$tmp\bootstrap\cache"
) | ForEach-Object {
    if (Test-Path $_) { Remove-Item "$_\*" -Recurse -Force -ErrorAction SilentlyContinue }
    if (-not (Test-Path $_)) { New-Item -ItemType Directory -Path $_ -Force | Out-Null }
    # Write .gitkeep so ZIP includes the directory (ZIP skips empty dirs)
    $gitkeep = "$_\.gitkeep"
    if (-not (Test-Path $gitkeep)) { New-Item -ItemType File -Path $gitkeep -Force | Out-Null }
}

# 3. composer install --no-dev in temp (creates vendor/)
if (-not (Test-Path "$tmp\vendor")) {
    Write-Host "Running composer install --no-dev ..."
    $php = (Get-Command php -ErrorAction SilentlyContinue)?.Source
    if (-not $php) { Write-Warning "PHP not found on PATH — vendor will be missing"; }
    else {
        Push-Location $tmp
        & composer install --no-dev --optimize-autoloader --no-interaction --quiet 2>&1
        Pop-Location
    }
} else {
    Write-Host "vendor/ already in temp (copied from src)"
}

# Remove bootstrap/cache PHP files — they contain absolute dev-machine paths
# and break Laravel on shared hosting (Target class [view] does not exist)
Get-ChildItem "$tmp\bootstrap\cache\*.php" -ErrorAction SilentlyContinue | Remove-Item -Force

# 4. ZIP from temp
if (Test-Path $out) { Remove-Item $out }
Add-Type -Assembly System.IO.Compression.FileSystem
$z = [System.IO.Compression.ZipFile]::Open($out, 'Create')

Get-ChildItem -Path $tmp -Recurse -File | ForEach-Object {
    $rel = $_.FullName.Substring($tmp.Length + 1)
    try {
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($z, $_.FullName, $rel) | Out-Null
    } catch {
        Write-Warning "Skipped: $rel — $_"
    }
}
$z.Dispose()

# 5. Cleanup temp
Remove-Item $tmp -Recurse -Force -ErrorAction SilentlyContinue

$size = [math]::Round((Get-Item $out).Length / 1MB, 2)
Write-Host "Done: $out ($size MB)"
