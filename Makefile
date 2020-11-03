SHELL := /bin/bash

tests:
	APP_ENV=test symfony console doctrine:fixture:load -n
	symfony php bin/phpunit

.PHONY: tests
