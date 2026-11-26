# =============================================================
# BOOKVERSE - MAKEFILE
# Cac lenh tat de quan ly Docker
#
# Cach dung (can cai 'make' tren Windows qua Chocolatey):
#   choco install make
# Hoac dung file scripts/docker.ps1 neu dung PowerShell
# =============================================================

.PHONY: help up down build rebuild restart logs ps shell-php shell-db shell-redis clean

## Hien thi huong dan
help:
	@echo "BookVerse Docker Commands:"
	@echo "  make up          - Khoi dong tat ca services"
	@echo "  make down        - Dung tat ca services"
	@echo "  make build       - Build lai tat ca images"
	@echo "  make rebuild     - Build lai tu dau (khong dung cache)"
	@echo "  make restart     - Restart tat ca services"
	@echo "  make logs        - Xem log tat ca services (Ctrl+C de thoat)"
	@echo "  make ps          - Xem trang thai containers"
	@echo "  make shell-php   - Mo terminal trong PHP container"
	@echo "  make shell-db    - Mo MySQL CLI"
	@echo "  make shell-redis - Mo Redis CLI"
	@echo "  make clean       - Xoa tat ca containers va volumes (NGUY HIEM)"

## Khoi dong
up:
	docker compose up -d

## Dung
down:
	docker compose down

## Build images (chi build lai cai thay doi)
build:
	docker compose build

## Build lai hoan toan tu dau
rebuild:
	docker compose build --no-cache

## Restart
restart:
	docker compose restart

## Xem log
logs:
	docker compose logs -f

logs-nginx:
	docker compose logs -f nginx

logs-php:
	docker compose logs -f wordpress

logs-db:
	docker compose logs -f db

logs-redis:
	docker compose logs -f redis

## Xem trang thai
ps:
	docker compose ps

## Mo terminal trong cac container
shell-php:
	docker exec -it bookverse_php sh

shell-db:
	docker exec -it bookverse_db mysql -u $${DB_USER:-bookverse_user} -p$${DB_PASSWORD:-BookversePass2026@} $${DB_NAME:-bookverse}

shell-nginx:
	docker exec -it bookverse_nginx sh

shell-redis:
	docker exec -it bookverse_redis redis-cli -a $${REDIS_PASSWORD:-Redis@BookVerse2026!}

## Xoa sach (NGUY HIEM: mat du lieu!)
clean:
	@echo "CANH BAO: Lenh nay se xoa tat ca containers va volumes!"
	@read -p "Ban co chac chan khong? (yes/no): " confirm && [ "$$confirm" = "yes" ] || exit 1
	docker compose down -v --remove-orphans
