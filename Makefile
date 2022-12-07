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
