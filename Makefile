.PHONY: all_targets
all_targets: build run composer-install migrate fixtures

build:
	docker-compose build

run:
	docker-compose up -d

composer-install:
	docker exec php composer install
	docker exec php yarn install
	docker exec php yarn run encore dev

migrate:
	docker exec php rm -rf migrations/*
	docker exec php php bin/console make:migration
	docker exec php php bin/console doctrine:migrations:migrate

fixtures:
	docker exec php php bin/console doctrine:fixtures:load --purge-with-truncate --no-interaction