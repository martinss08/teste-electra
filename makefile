CONTEINER=laradocker_app

setup:
	docker compose up -d --build
	docker exec -it $(CONTEINER) composer install 
	docker exec -it $(CONTEINER) php artisan key:generate --force
	docker exec -it $(CONTEINER) php artisan migrate --force

test:
	docker exec -it $(CONTEINER) php artisan test

down:
	docker compose down