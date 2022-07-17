VENDOR = vendor/bin

##
## run refactoring
##
check:
	refactoring --keep-going


refactoring: eslint php-cs-fix

run-tests:
	sh ./bin/run-tests.sh

eslint:
	sh node_modules/.bin/eslint assets/js/ --ext .js,.vue --fix

phpstan:
	sh ${VENDOR}/phpstan analyze src/ --level=4


php-cs-fix:
	sh ${VENDOR}/php-cs-fixer fix src/ --allow-risky=yes --dry-run --diff --verbose
