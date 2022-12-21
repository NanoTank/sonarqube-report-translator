# Powercloud Report Translator for Sonarqube

## Introduction
This project is meant to bridge the output formats of PHPCS and PHPMD reports to Sonarqube's generic input format.

## Precondition
Have a working Docker environment installed and run Makefile targets from outside the container:
```shell
make build
```
to build the environment and
```shell
make install
```
to install Composer dependencies.

## Tests
This project is using PHPUnit to run automated tests. Run the tests easily by executing Composer scripts. You can run unit tests and functional tests separately:
```shell
composer test-unit
```
and 
```shell
composer test-functional
```


