.DEFAULT_GOAL : help

.PHONY: help
help: ## Помощь
	@printf "\033[33m%s:\033[0m\n" 'Available commands'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z0-9_-]+:.*?## / {printf "  \033[32m%-18s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.PHONY: phpunit
phpunit: ## Проверка phpunit
	php vendor/bin/phpunit --no-coverage --dont-report-useless-tests --colors=never --do-not-cache-result

.PHONY: cs
cs: ## Проверка cs-fixer
	php ./vendor/bin/php-cs-fixer check

.PHONY: format
format: ## Отформатировать проект
	php ./vendor/bin/php-cs-fixer fix