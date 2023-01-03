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

## Usage
You can implement this translator in your Jenkins script in order to execute it in your CI/CD pipeline.
Just execute the appropriate translator command from within the Docker container to get a valid 
Generic Input Format Report generated:
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
to let the translator check for the given input format.

## Tests
This project is using PHPUnit to run automated tests. 
Run the tests easily by executing Composer scripts. 
There are 2 test suites so that you can run unit tests and functional tests separately:
```shell
composer test-unit
```
and 
```shell
composer test-functional
```
or just run both tests suites at once and get a code coverage report created in *tests/Output* directory:
```shell
composer test-all
```


