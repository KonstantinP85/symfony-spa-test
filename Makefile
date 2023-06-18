.PHONY: all_targets
all_targets: build run composer-install #migrate fixtures

build:
	docker-compose build

run:
	docker-compose up -d

composer-install:
	docker exec php composer install
	docker exec php yarn install
	docker exec php yarn run encore dev

#migrate:
	#docker exec db-postgres psql -U postgres -c "DROP DATABASE IF EXISTS projectdb"
	#docker exec php-8.1 rm -rf src/Infrastructure/Migrations/*
	#docker exec php-8.1 php bin/console doctrine:database:create
	#docker exec php-8.1 php bin/console make:migration
	#docker exec php-8.1 php bin/console doctrine:migrations:migrate

#fixtures:
	#docker exec php-8.1 php bin/console doctrine:fixtures:load --purge-with-truncate --no-interaction