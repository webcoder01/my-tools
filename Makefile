init:
	composer install
	php bin/console tailwind:init
	symfony server:ca:install

start:
	docker-compose up -d
	symfony server:start

build-tailwind:
	php bin/console tailwind:build --watch

stop:
	docker-compose down
