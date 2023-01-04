SHELL := bash
DOCKER := docker
DOCKER_COMPOSE := docker-compose -f docker/docker-compose.yml
ECHO := echo

build:
	$(DOCKER_COMPOSE) build app
.PHONY: build

install:
	$(DOCKER_COMPOSE) run --rm app composer install
.PHONY: install

start:
	$(DOCKER_COMPOSE) up --remove-orphans -d app
.PHONY: start

shell:
	$(DOCKER_COMPOSE) run --rm app sh
.PHONY: shell

sonar-deptrac:
	$(DOCKER_COMPOSE) run --rm app composer ci-deptrac || exit 0
	$(DOCKER_COMPOSE) run --rm app bin/console srt:translate:deptrac tests/Output/deptrac.json tests/Output/deptrac-sonar.json
.PHONY: sonar-deptrac

sonar-phpcs:
	$(DOCKER_COMPOSE) run --rm app composer ci-phpcs || exit 0
	$(DOCKER_COMPOSE) run --rm app bin/console srt:translate:phpcs tests/Output/phpcs.json tests/Output/phpcs-sonar.json
.PHONY: sonar-phpcs

sonar-phpmd:
	$(DOCKER_COMPOSE) run --rm app composer ci-phpmd || exit 0
	$(DOCKER_COMPOSE) run --rm app bin/console srt:translate:phpmd tests/Output/phpmd.json tests/Output/phpmd-sonar.json
.PHONY: sonar-phpcs

sonar-scanner-local:
	$(DOCKER_COMPOSE) run --rm sonar-scanner-local
.PHONY: sonar-scanner-local
