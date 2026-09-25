.PHONY: up down logs migrate test lint contract fe-lint
up:
	docker compose -f infra/docker-compose.yml --env-file infra/.env up -d --build
down:
	docker compose -f infra/docker-compose.yml --env-file infra/.env down
logs:
	docker compose -f infra/docker-compose.yml --env-file infra/.env logs -f --tail=100
migrate:
	docker compose -f infra/docker-compose.yml --env-file infra/.env exec api php artisan migrate --seed
test:
	docker compose -f infra/docker-compose.yml --env-file infra/.env exec api php artisan test --parallel
lint:
	docker compose -f infra/docker-compose.yml --env-file infra/.env exec api sh -c "vendor/bin/pint --test && vendor/bin/phpstan analyse && vendor/bin/deptrac analyse"
contract:
	npx --yes @redocly/cli lint contracts/openapi.yaml
fe-lint:
	cd frontend && npx ng lint && npx ng test --watch=false
