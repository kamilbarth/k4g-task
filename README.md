# k4g-task

Task rekrutacyjny dla k4g

## Wymagania

Przed uruchomieniem projektu upewnij się, że masz zainstalowane:

- Docker
- Docker Compose

## Uruchomienie Projektu

Aby uruchomić projekt, wykonaj następujące kroki:

1. `docker-compose up -d`
2. `composer install`
3. `php bin/console doctrine:migrations:migrate`
4. `php bin/console doctrine:fixtures:load`
5. `symfony serve`