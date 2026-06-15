param([string]$Out = "releases\dravion-v1.2.11-full.zip")

$src = $PSScriptRoot
$zip = Join-Path $src $Out
if (Test-Path $zip) { Remove-Item $zip }

Add-Type -Assembly System.IO.Compression.FileSystem
$z = [System.IO.Compression.ZipFile]::Open($zip, 'Create')

Get-ChildItem -Path $src -Recurse -File | Where-Object {
    $rel = $_.FullName.Substring($src.Length + 1)
    $rel -notmatch '^\.git' -and
    $rel -notmatch '^node_modules' -and
    $rel -notmatch '^releases' -and
    $rel -ne '.env' -and
    $rel -notlike '*.log' -and
    $rel -ne 'make-full-zip.ps1'
} | ForEach-Object {
    $rel = $_.FullName.Substring($src.Length + 1)
    try {
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($z, $_.FullName, $rel) | Out-Null
    } catch {}
}

$z.Dispose()
Write-Host "Done: $zip"
