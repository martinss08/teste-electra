CONTEINER=laradocker_app

.PHONY: setup frontend test down stop-frontend restart-frontend

setup:
	docker compose up -d --build
	docker exec -it $(CONTEINER) sh -c "cp .env.example .env"
	docker exec -it $(CONTEINER) composer install
	docker exec -it $(CONTEINER) php artisan key:generate --force
	docker exec -it $(CONTEINER) sh -c "chmod -R 777 storage bootstrap/cache"
	docker exec -it $(CONTEINER) sh -c "chown -R www-data:www-data storage bootstrap/cache"
	docker exec -it $(CONTEINER) php artisan migrate:fresh --seed --force
	$(MAKE) --no-print-directory restart-frontend

frontend:
	@cd frontend && nohup sh -c "npm install && npm run dev -- --host" > vite.log 2>&1 &
	@echo "Frontend iniciado em background"
	@echo "Acesse: http://localhost:5173"

restart-frontend:
	$(MAKE) --no-print-directory stop-frontend
	$(MAKE) --no-print-directory frontend


seed:
	docker exec -it $(CONTEINER) php artisan db:seed --force

test:
	docker exec -it $(CONTEINER) php artisan test

down:
	docker compose down
	$(MAKE) --no-print-directory stop-frontend