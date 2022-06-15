phpstan:
	php -d memory_limit=-1 vendor/bin/phpstan.phar analyse -l max -c phpstan.neon src
