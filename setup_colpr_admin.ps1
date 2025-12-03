# ColPR Admin Setup Script for XAMPP
# Run this script as Administrator

Write-Host "ColPR Admin Setup Script" -ForegroundColor Green
Write-Host "=========================" -ForegroundColor Green
Write-Host ""

# Check if running as Administrator
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "Right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Yellow
    exit 1
}

# Check if XAMPP htdocs exists
$htdocsPath = "C:\xampp\htdocs"
if (-not (Test-Path $htdocsPath)) {
    Write-Host "ERROR: XAMPP htdocs folder not found at: $htdocsPath" -ForegroundColor Red
    Write-Host "Please install XAMPP or update the path in this script." -ForegroundColor Yellow
    exit 1
}

# Check if source folder exists
$sourcePath = "E:\final\ColPR_admin"
if (-not (Test-Path $sourcePath)) {
    Write-Host "ERROR: Source folder not found at: $sourcePath" -ForegroundColor Red
    Write-Host "Please check the path and try again." -ForegroundColor Yellow
    exit 1
}

# Check if link already exists
$linkPath = "$htdocsPath\ColPR_admin"
if (Test-Path $linkPath) {
    Write-Host "WARNING: A folder or link already exists at: $linkPath" -ForegroundColor Yellow
    $response = Read-Host "Do you want to remove it and create a new link? (y/n)"
    if ($response -eq 'y' -or $response -eq 'Y') {
        Remove-Item $linkPath -Force -Recurse -ErrorAction SilentlyContinue
        Write-Host "Removed existing folder/link." -ForegroundColor Green
    } else {
        Write-Host "Aborted." -ForegroundColor Yellow
        exit 0
    }
}

# Create symbolic link
Write-Host ""
Write-Host "Creating symbolic link..." -ForegroundColor Cyan
Write-Host "  From: $sourcePath" -ForegroundColor Gray
Write-Host "  To:   $linkPath" -ForegroundColor Gray

try {
    New-Item -ItemType SymbolicLink -Path $linkPath -Target $sourcePath -Force | Out-Null
    Write-Host ""
    Write-Host "SUCCESS! Symbolic link created successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Yellow
    Write-Host "1. Make sure Apache and MySQL are running in XAMPP Control Panel" -ForegroundColor White
    Write-Host "2. Access your application at: http://localhost/ColPR_admin/index.php" -ForegroundColor White
    Write-Host "   Or login at: http://localhost/ColPR_admin/login.php" -ForegroundColor White
    Write-Host ""
} catch {
    Write-Host ""
    Write-Host "ERROR: Failed to create symbolic link!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host ""
    Write-Host "Alternative: Copy the folder manually to: $htdocsPath" -ForegroundColor Yellow
    exit 1
}


