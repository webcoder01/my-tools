init:
	composer install
	npm install
	npx husky init
	php bin/console tailwind:init
	symfony server:ca:install

start:
	docker-compose up -d
	symfony server:start

build-tailwind:
	php bin/console tailwind:build --watch

add-migrations:
	php bin/console doctrine:migrations:diff

migrate:
	php bin/console doctrine:migrations:migrate

load-fixtures:
	php bin/console doctrine:fixtures:load

tests:
	php bin/phpunit tests

stop:
	docker-compose down
