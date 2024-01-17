init:
	symfony server:ca:install

start:
	docker-compose up -d
	symfony server:start

stop:
	docker-compose down