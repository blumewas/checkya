set dotenv-load := false

# default recipe to display help information
default:
  @just --list

# Initial project setup
setup:
	cp -n .env.example .env
	@test -d public/storage || (cd public && ln -s ../storage/app/public storage)
	composer install
	php artisan key:generate
	php artisan migrate:fresh --seed
	npm install
	npm run build
	php ./vendor/bin/grumphp git:init

# Lint files
@lint:
	./vendor/bin/ecs check --fix
	./vendor/bin/php-cs-fixer fix
	./vendor/bin/rector process
	./vendor/bin/tlint lint

# Check code quality
@quality:
	./vendor/bin/phpstan analyse --memory-limit=2G

# Run unit and integration tests
@test:
	echo "Running unit and integration tests"; \
	vendor/bin/pest

# Run tests and create code-coverage report with Xdebug
@coverage:
	echo "Running unit and integration tests with coverage"; \
	vendor/bin/pest --coverage --min=80 --compact
	just type-coverage

# Run type coverage
@type-coverage:
	vendor/bin/pest --type-coverage --min=80 --compact
