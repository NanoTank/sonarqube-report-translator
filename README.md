# Powercloud Report Translator for Sonarqube

## Introduction
This project is meant to bridge the output formats of
**[DEPTRAC](https://github.com/qossmic/deptrac)**,
**[PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)**
and **[PHP Mess Detector](https://phpmd.org/)** reports to
**[Sonarqube's](https://www.sonarsource.com/products/sonarqube/)**
[generic issue input format](https://docs.sonarqube.org/latest/analyzing-source-code/importing-external-issues/generic-issue-import-format/).
It is based on **[Symfony](https://symfony.com/)** 6,
uses **[PHPUnit](https://phpunit.de/)** for automated testing and
**[Composer](https://getcomposer.org/)**.

## Precondition
Assuming you have a working 
**[Docker](https://www.docker.com/)** environment installed, we've created some Makefile targets for your convenience.
Navigate into this project directory and run:
```shell
make build
```
to build the environment,
```shell
make install
```
to install Composer dependencies and
```shell
make shell
```
to open a shell in your Docker container.

## Usage
You can implement this translator in your Jenkins script in order to run it in your CI/CD pipeline.
Just execute the appropriate translator command from within the Docker container to get a valid 
Generic Issue Input Format report generated:
```shell
bin/console srt:translate:deptrac path/to/input/file.json path/to/output/file.json [--type] [--severity] 
```
```shell
bin/console srt:translate:phpcs path/to/input/file.json path/to/output/file.json [--type] [--severity] 
```
```shell
bin/console srt:translate:phpmd path/to/input/file.json path/to/output/file.json [--type] [--severity] 
```
With the optional switches *type* and *severity* you can override the output values of your report
in case you need to. You can also run
```shell
bin/console srt:translate path/to/input/file.json path/to/output/file.json [--type] [--severity] 
```
to let the translator choose the right translator for you by given input format.

## Tests
This project is using PHPUnit to run automated tests in your Docker container. 
There are 2 test suites so that you can run unit tests and functional tests separately.
For your convenience we've created Composer scripts:
```shell
composer test-unit
```
and 
```shell
composer test-functional
```
Or just run both tests suites at once and get an HTML code coverage report created in *tests/Output* directory:
```shell
composer test-all
```


