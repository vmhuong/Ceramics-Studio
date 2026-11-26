# =============================================================
# BOOKVERSE - DOCKER MANAGEMENT SCRIPT (Windows PowerShell)
# Dung cho nguoi dung Windows khong co lenh 'make'
#
# Cach dung:
#   .\scripts\docker.ps1 up
#   .\scripts\docker.ps1 down
#   .\scripts\docker.ps1 logs
# =============================================================

param(
    [Parameter(Position=0)]
    [string]$Command = "help"
)

$ErrorActionPreference = "Continue"

function Show-Help {
    Write-Host "BookVerse Docker Commands:" -ForegroundColor Cyan
    Write-Host "  up          - Khoi dong tat ca services" -ForegroundColor White
    Write-Host "  down        - Dung tat ca services" -ForegroundColor White
    Write-Host "  build       - Build lai images (giu cache)"  -ForegroundColor White
    Write-Host "  rebuild     - Build lai tu dau (khong cache)" -ForegroundColor White
    Write-Host "  restart     - Restart tat ca services" -ForegroundColor White
    Write-Host "  logs        - Xem log theo thoi gian thuc" -ForegroundColor White
    Write-Host "  ps          - Xem trang thai containers" -ForegroundColor White
    Write-Host "  stats       - Xem CPU/RAM tung container" -ForegroundColor White
    Write-Host "  shell-php   - Mo terminal trong PHP container" -ForegroundColor White
    Write-Host "  shell-db    - Mo MySQL CLI" -ForegroundColor White
    Write-Host "  shell-redis - Mo Redis CLI" -ForegroundColor White
    Write-Host ""
    Write-Host "Vi du: .\scripts\docker.ps1 up" -ForegroundColor Yellow
}

switch ($Command) {
    "up" {
        Write-Host "Dang khoi dong BookVerse..." -ForegroundColor Green
        docker compose up -d
        Write-Host ""
        docker compose ps
    }
    "down" {
        Write-Host "Dang dung BookVerse..." -ForegroundColor Yellow
        docker compose down
    }
    "build" {
        Write-Host "Dang build images..." -ForegroundColor Cyan
        docker compose build
    }
    "rebuild" {
        Write-Host "Dang build lai tu dau (se mat vai phut)..." -ForegroundColor Cyan
        docker compose build --no-cache
    }
    "restart" {
        Write-Host "Dang restart tat ca services..." -ForegroundColor Yellow
        docker compose restart
        docker compose ps
    }
    "logs" {
        Write-Host "Xem log (nhan Ctrl+C de thoat)..." -ForegroundColor Gray
        docker compose logs -f
    }
    "logs-php" { docker compose logs -f wordpress }
    "logs-nginx" { docker compose logs -f nginx }
    "logs-db" { docker compose logs -f db }
    "ps" {
        docker compose ps
    }
    "stats" {
        Write-Host "CPU/RAM usage (nhan Ctrl+C de thoat):" -ForegroundColor Cyan
        docker stats
    }
    "shell-php" {
        Write-Host "Mo terminal PHP container..." -ForegroundColor Cyan
        docker exec -it bookverse_php sh
    }
    "shell-db" {
        Write-Host "Mo MySQL CLI..." -ForegroundColor Cyan
        docker exec -it bookverse_db mysql -u bookverse_user -pBookversePass2026@ bookverse
    }
    "shell-nginx" {
        docker exec -it bookverse_nginx sh
    }
    "shell-redis" {
        Write-Host "Mo Redis CLI..." -ForegroundColor Cyan
        docker exec -it bookverse_redis redis-cli -a "Redis@BookVerse2026!"
    }
    default {
        Show-Help
    }
}
