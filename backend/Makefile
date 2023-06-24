# Paramètre du Makefile
# --------------------
PROJECT = ./backend
AUTHOR = Papoel
HTTP_PORT = 8500
HOST_NAME = 127.0.0.1
DB_NAME = db_plongee
DB_USER = root
DB_PASS = password
DB_PORT = 3306
DB_SERVER = MariaDB-10.9.3&charset=utf8mb4
DATABASE_URL = mysql://$(DB_USER):$(DB_PASS)@$(HOST_NAME):$(DB_PORT)/$(DB_NAME)
#?serverVersion=$(DB_VERSION)&charset=utf8mb4

# --------------------
# Commandes
# --------------------
# Commande PHP
PHP = php
# Commande Composer
COMPOSER = composer
# Commande Symfony
SYMFONY = $(PHP) bin/console
# Commande Yarn
YARN = yarn
# Binaire de Symfony
SYMFONY_BIN = symfony
# Linter de Symfony
SYMFONY_LINT = $(SYMFONY) lint:

# --------------------
# Commandes locales
# --------------------
# Commande Symfony locale
BREW = brew
DOCKER = docker
DOCKER_COMPOSE = docker-compose
DOCKER_RUN = $(DOCKER) run
PHP_CS_FIXER = php-cs-fixer
BIN_VENDOR = vendor/bin/
MAKE = make

# --------------------
# PHPQA
# --------------------
PHPQA = jakzal/phpqa:php8.2
PHPQA_RUN = $(DOCKER_RUN) --init --rm -v $(PWD):/project -w /project $(PHPQA)
# --------------------

# --------------------
# PHPUNIT
# --------------------
PHPUNIT = APP_ENV=test $(SYMFONY_BIN) php bin/phpunit
# --------------------

# --------------------
# Afficher l'aide avec du style en utilisant @agrep
# --------------------
help: ## Afficher l'aide
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

# --------------------
# Commandes Docker 🐳
# --------------------
## ********** 🐳 DOCKER 🐳 ******************************************************************
up: ## Lancer Docker | docker-compose up -d
	$(DOCKER_COMPOSE) up -d

down: ## Arrêter Docker | docker-compose down --remove-orphans
	$(DOCKER_COMPOSE) down --remove-orphans

d-restart: ## Redémarrer Docker | up + down
	$(MAKE) down
	$(MAKE) up
.PHONY: d-restart

# --------------------
# Commandes Symfony 🐘
# --------------------
## ********** 🐘 SYMFONY 🐘 ******************************************************************
sf: ## Afficher toutes les commandes de Symfony | symfony
	$(SYMFONY)

install: ## Installer les dépendances | composer install
	$(COMPOSER) install

serve: ## Lancer le serveur | symfony server:start -d
	$(SYMFONY_BIN) server:start -d

stop: ## Arrêter le serveur | symfony server:stop
	$(SYMFONY_BIN) server:stop

open: ## Ouvrir le site | symfony open:local
	$(SYMFONY_BIN) open:local

test: ## Créer un test unitaire | symfony console make:test
	$(SYMFONY) make:test

server-restart: ## Redémarrer le serveur | stop + start
	stop start
.PHONY: server-restart

# --------------------
# Commandes de création avec le MakerBundle
# --------------------
## ********** 🛠️ MAKER 🛠️ ******************************************************************
entity: ## Créer une entité | symfony console make:entity
	$(SYMFONY) make:entity

controller: ## Créer un contrôleur | symfony console make:controller
	$(SYMFONY) make:controller

form: ## Créer un formulaire | symfony console make:form
	$(SYMFONY) make:form

crud: ## Créer un CRUD | symfony console make:crud
	$(SYMFONY) make:crud

command: ## Créer une commande | symfony console make:command
	$(SYMFONY) make:command

# --------------------
# Commandes de t.ests
# --------------------
## ********** 🧪 TESTS 🧪 ******************************************************************
tests: ## Lancer les tests | php bin/phpunit
	$(PHPUNIT) --testdox
.PHONY: tests

tests-coverage: ## Lancer les tests avec le coverage | php bin/phpunit --coverage-html var/coverage
	XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html var/coverage
.PHONY: tests-coverage
test-watch: ## Lancer les tests en mode watch | php bin/phpunit --watch
	$(PHP) bin/phpunit --watch

# --------------------
# Commandes de développement
# --------------------
## ********** 🛠️ DÉVELOPPEMENT 🛠️ ******************************************************************
yarn: ## Installer les dépendances | yarn install
	$(YARN) install

yarn-build: ## Compiler les assets | yarn encore dev
	$(YARN) encore dev

yarn-watch: ## Compiler les assets en mode watch | yarn encore dev --watch
	$(YARN) encore dev --watch

yarn-prod: ## Compiler les assets pour la production | yarn encore production
	$(YARN) encore production

# --------------------
# Commandes de sécurité
# --------------------
## ********** 🔒 SÉCURITÉ 🔒 ******************************************************************
security-check: ## Vérifier les vulnérabilités | symfony console security:check
	$(SYMFONY) security:check

# --------------------
# Commandes de base de données
# --------------------
## ********** 🗄️ BASE DE DONNÉES 🗄️ ******************************************************************
db-create-test: ## Créer la base de données de test | symfony console doctrine:database:create --env=test
	$(SYMFONY) doctrine:database:create --env=test

db-drop-test: ## Supprimer la base de données de test | symfony console doctrine:database:drop --force --env=test
	$(SYMFONY) doctrine:database:drop --force --env=test

db-schema: ## Créer le schéma de la base de données | symfony console doctrine:schema:create
	$(SYMFONY) doctrine:schema:create

db-schema-test: ## Créer le schéma de la base de données de test | symfony console doctrine:schema:create --env=test
	$(SYMFONY) doctrine:schema:create --env=test

db-drop-schema: ## Supprimer le schéma de la base de données | symfony console doctrine:schema:drop --force
	$(SYMFONY) doctrine:schema:drop --force --if-exists

db-drop-schema-test: ## Supprimer le schéma de la base de données de test | symfony console doctrine:schema:drop --force --env=test
	$(SYMFONY) doctrine:schema:drop --force --env=test

db-s-v: ## Valider le schéma de la base de données | symfony console doctrine:schema:validate
	$(SYMFONY) doctrine:schema:validate

db-s-u: ## Mettre à jour le schéma de la base de données | symfony console doctrine:schema:update --force
	$(SYMFONY) doctrine:schema:update --force

db-s-u-test: ## Mettre à jour le schéma de la base de données de test | symfony console doctrine:schema:update --force --env=test
	$(SYMFONY) doctrine:schema:update --force --env=test

schema-load-db: ## Charger le schéma de la base de données | schema-validate-db + schema-update-db
	schema-validate-db schema-update-db

doctrine-help: ## Afficher toutes les commandes de Doctrine | doctrine
	$(SYMFONY) doctrine

migration: ## Créer une migration | symfony console make:migration
	$(SYMFONY) make:migration

migrate: ## Exécuter les migrations | symfony console doctrine:migrations:migrate -n
	$(SYMFONY) doctrine:migrations:migrate -n

fixtures: ## Créer des fixtures | symfony console make:fixtures
	$(SYMFONY) make:fixtures

init-db:
	$(MAKE) db-drop
	$(MAKE) db-create
	@if [ -n "$$(find migrations -name 'Version*' -print -quit)" ]; then \
		$(SYMFONY) doctrine:migrations:migrate --no-interaction; \
	else \
		$(SYMFONY) doctrine:schema:validate; \
		$(SYMFONY) doctrine:schema:update --force; \
	fi
	@read -p "Charger les Fixtures ? (y/n): " answer; \
	if [ "$$answer" = "y" ]; then \
		$(MAKE) fixtures-load; \
	else \
		echo "Exiting..."; \
		exit 0; \
	fi

db-drop: ## Supprimer la base de données | symfony console doctrine:database:drop --force
	$(SYMFONY) doctrine:database:drop --if-exists --force

db-create: ## Créer la base de données | symfony console doctrine:database:create
	$(SYMFONY) doctrine:database:create

fixtures-load:
	$(SYMFONY) doctrine:fixtures:load --no-interaction
.PHONY: init-db db-drop db-create fixtures-load

delete-db: ## Supprimer la base de données | db-drop-schema + db-drop
	$(MAKE) db-drop-schema
	$(MAKE) db-drop

init-db-tests: ## Initialiser la base de données de test | create-db-tests + schema-load-db
	cc db-drop-test db-create-test db-schema-test db-s-v db-s-u-test

# --------------------
# Commandes de debug
# --------------------
## ********** 🐛 DEBUG 🐛 ******************************************************************
debug: ## Afficher les variables d'environnement | symfony console debug:cotainer
	$(SYMFONY) debug:container

debug-routes: ## Afficher les routes | symfony console debug:router
	$(SYMFONY) debug:router

debug-config: ## Afficher la configuration | symfony console debug:config
	$(SYMFONY) debug:config

debug-env: ## Afficher les variables d'environnement | symfony console debug:config
	$(SYMFONY) debug:doten6ec	$(SYMFONY) debug:config
schemaspy: ## Générer la documentation de la base de données avec SchemaSpy
	$(DOCKER_COMPOSE) run --rm \
	  -e SCHEMASPY_OUTPUT_DIR=/output \
	  schemaspy \
		-t mariadb \
		-host mariadb \
		-db $(DB_NAME) \
		-all \
		-u $(DB_USER) \
		-p $(DB_PASS)
.PHONY: schemaspy

# --------------------
# Commandes de cache
# --------------------
## ********** 🗑️ CACHE 🗑️ ******************************************************************
cc: ## Vider le cache | symfony console cache:clear
	$(SYMFONY) cache:clear

cc-prod: ## Vider le cache de production | symfony console cache:clear --env=prod
	$(SYMFONY) cache:clear --env=prod

cc-test: ## Vider le cache de test | symfony console cache:clear --env=test
	$(SYMFONY) cache:clear --env=test

cc-warmup: ## Précharger le cache | symfony console cache:warmup
	$(SYMFONY) cache:warmup

# --------------------
# Qualité du code
# --------------------
## ********** 📝 QUALITÉ DU CODE 📝 ******************************************************************
# --------------------
# Utilisation de PHPQA
# --------------------
qa-cs-fixer-dry-run: ## Execute php-cs-fixer in dry-run mode.
	$(PHPQA_RUN) php-cs-fixer fix ./src --rules=@Symfony --verbose --dry-run
.PHONY: qa-cs-fixer-dry-run

qa-cs-fixer: ## Execute php-cs-fixer.
	$(PHPQA_RUN) php-cs-fixer fix ./src --rules=@Symfony --verbose
.PHONY: qa-cs-fixer

qa-phpstan: ## Execute phpstan.
	$(PHPQA_RUN) phpstan analyse ./src --level=9
.PHONY: qa-phpstan

qa-security-checker: ## Vériier les dépendances avec security-checker.
	$(SYMFONY_BIN) security:check
.PHONY: qa-security-checker

qa-phpcpd: ## Execute phpcpd (Copier/Coller Detector).
	$(PHPQA_RUN) phpcpd ./src
.PHONY: qa-phpcpd

qa-php-metrics: ## Execute phpmetrics, génère un rapport html dans var/phpmetrics.
	$(PHPQA_RUN) phpmetrics --report-html=var/phpmetrics ./src
.PHONY: qa-php-metrics

qa-lint-twigs: ## Vérifier la syntaxe des fichiers twig.
	$(SYMFONY_LINT)twig templates
.PHONY: qa-lint-twigs

qa-lint-yaml: ## Vérifier la syntaxe des fichiers yaml.
	$(SYMFONY_LINT)yaml config
.PHONY: qa-lint-yaml

qa-lint-container: ## Vérifier la configuration du container.
	$(SYMFONY_LINT)container
.PHONY: qa-lint-container

qa-lint-schema: ## Vérifier la validité du schéma de la base de données.
	$(SYMFONY) doctrine:schema:validate
.PHONY: qa-lint-schema

qa-audit: ## Faire un audit de sécurité avec composer.
	$(COMPOSER) audit
.PHONY: qa-audit

# --------------------
# Sans utiliser PHPQA
# --------------------
fix: ## Vérifier la qualité du code | php-cs-fixer fix
	$(PHP_CS_FIXER) fix

phpstan: ## Vérifier la qualité du code | phpstan
	$(BIN_VENDOR)phpstan

check: ## Vérifier la qualité du code | phpstan + php-cs-fixer fix --dry-run
	$(MAKE) phpstan # Coller la commande si il y a un espace cela échoue
	$(MAKE) pint
	$(MAKE) lint

pint: ## Vérifier la qualité du code |
	$(BIN_VENDOR)pint -v

lint: ## Vérifier la qualité du code | symfony console lint:twig, yaml (ignore vendor)
	$(SYMFONY) lint:twig templates
	$(SYMFONY) lint:yaml ./*.yaml config
# --------------------

# --------------------
# Commandes pratique
# --------------------
before-commit: qa-cs-fixer qa-phpstan qa-security-checker qa-phpcpd qa-lint-twigs qa-lint-yaml qa-lint-container qa-lint-schema tests ## Run before commit.
.PHONY: before-commit
# --------------------

# --------------------
# Commandes du projet
# --------------------
## ********** 📦 PROJET 📦 ******************************************************************
# Création copie .env et le nommé en .en.local ne garder que les variables d'environnement du projet
create-env: ## Créer le fichier .env.local | cp .env .env.local
	sed -n -e '/^APP_ENV/p' -e '/^APP_SECRET/p' -e '/^MESSENGER_TRANSPORT_DSN/p' .env > .env.local \
    	&& printf "\n###> Database ###\nDATABASE_URL=mysql://%s:@%s:%s/%s\n###< Database ###\n" \
		"$(DB_USER)" "$(HOST_NAME)" "$(DB_PORT)" "$(DB_NAME)?serverVersion=$(DB_SERVER)" >> .env.local

start: up serve open ## Lancer le projet | up + serve + open

stop: stop down ## Arrêter le projet | down

first-install: ## Première installation du Projet (Activer ce qui est necessaire).
	$(COMPOSER) install    # Installe dépendances de Composer
	# $(YARN)     install    # Installe dépendances de Yarn
	# $(YARN)     build      # Compile les assets
	$(MAKE)     up         # Lance les containers
	$(MAKE)     create-env  # Créer le fichier .env.local et DATABASE_URL
	$(MAKE)     init-db    # Initialise la base de données
	# $(MAKE)     serve      # Lance le serveur et ouvre le navigateur
	# $(YARN)     dev-server # Lance le serveur de développement de Yarn
.PHONY: first-install

# --------------------
# Canvas
# --------------------
## ********** 🎨 CANVAS 🎨 ******************************************************************
initCanvas:## Création du fichier de configuration de Canvas | echo "version: \"7.0.5\"" > canvas.yaml
	if [ -f "canvas.yaml" ]; then \
		echo "Une version de Canvas est déjà configurée:"; \
		version=$$(awk '/version:/ {print $$2}' canvas.yaml); \
		echo "$${version}"; \
		read -p "Voulez-vous modifier la version de Canvas? (y/N): " response; \
		if [ "$${response}" = "y" ] || [ "$${response}" = "Y" ]; then \
			read -p "Entrer la version de Canvas que vous souhaitez utiliser (ex: 7.0.5): " canvas_version; \
			echo "version: \"$${canvas_version}\"" > canvas.yaml; \
			echo "Version Canvas $${canvas_version} configurée dans le fichier canvas.yaml"; \
		else \
			echo "Fichier canvas.yaml inchangé."; \
		fi \
	else \
	  read -p "Entrer la version de Canvas que vous souhaitez utiliser (ex: 7.0.5): " canvas_version; \
  	  echo "version: \"$${canvas_version}\"" > canvas.yaml; \
	  echo "Version Canvas $${canvas_version} configurée dans le fichier canvas.yaml"; \
	fi
.PHONY: initCanvas
