.DEFAULT_GOAL:=help

.PHONY: build
build:
	cd docker && docker-compose build

.PHONY: up
up:
	cd docker && docker-compose up -d

.PHONY: down
down:
	cd docker && docker-compose down

.PHONY: logs
logs:
	cd docker && docker-compose logs -f

.PHONY: bash
bash:
	cd docker && docker-compose exec php-fpm bash

.PHONY: install
install:
	cd docker && docker-compose run --rm php-fpm sh -c 'composer install --no-interaction --no-suggest --ansi'

.PHONY: test
test: phpunit behat

.PHONY: phpunit
phpunit:
	cd docker && docker-compose run --rm php-fpm sh -c 'vendor/bin/phpunit --testdox --exclude-group=none --colors=always'

.PHONY: behat
behat:
	cd docker && docker-compose run --rm php-fpm sh -c 'vendor/bin/behat'

.PHONY: cs
cs:
	cd docker && docker-compose run --rm php-fpm sh -c 'vendor/bin/php-cs-fixer fix --no-interaction --diff --verbose'

.PHONY: stan
stan:
	cd docker && docker-compose run --rm php-fpm sh -c 'vendor/bin/phpstan analyse --memory-limit=-1'
