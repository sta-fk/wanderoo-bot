# Project Variables
COMPOSE=docker-compose
DOCKER=docker
PHP_CONTAINER=wanderoo_php

# Default target
.DEFAULT_GOAL := help

## —— Docker 🐳 ——
.PHONY: up down restart logs shell

up:  ## Start all containers in the background
	$(COMPOSE) up -d

down:  ## Stop and remove all containers
	$(COMPOSE) down --remove-orphans

restart:  ## Restart all containers
	$(COMPOSE) restart

logs:  ## Show logs for all containers
	$(COMPOSE) logs -f

shell:  ## Enter PHP container shell
	$(DOCKER) exec -it $(PHP_CONTAINER) bash

## —— Application Setup 🛠 ——
.PHONY: install migrate seed

install:  ## Install dependencies
	$(DOCKER) exec $(PHP_CONTAINER) composer install --no-interaction --optimize-autoloader

migrate:  ## Run database migrations
	$(DOCKER) exec $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction

seed:  ## Load initial test data
	$(DOCKER) exec $(PHP_CONTAINER) php bin/console doctrine:fixtures:load --no-interaction

## —— Database 🎲 ——
.PHONY: db-reset db-dump db-import

db-reset:  ## Drop and recreate the database
	$(DOCKER) exec $(PHP_CONTAINER) php bin/console doctrine:database:drop --force
	$(DOCKER) exec $(PHP_CONTAINER) php bin/console doctrine:database:create
	$(DOCKER) exec $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction
	$(DOCKER) exec $(PHP_CONTAINER) php bin/console doctrine:fixtures:load --no-interaction

## —— Quality Assurance ✅ ——
.PHONY: cs phpstan test

cs:  ## Run PHP Code Sniffer
	$(DOCKER) exec $(PHP_CONTAINER) vendor/bin/php-cs-fixer fix src

phpstan:  ## Run PHPStan static analysis
	$(DOCKER) exec $(PHP_CONTAINER) vendor/bin/phpstan analyse src --level=max

test:  ## Run PHPUnit tests
	$(DOCKER) exec $(PHP_CONTAINER) vendor/bin/phpunit
