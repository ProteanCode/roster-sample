# To start the project (takes port 80)

```shell
cd .docker
docker-compose -p roster up
```

# Install dependencies
```shell
docker exec roster_php_1 composer install
```

# Set env file
```shell
docker exec roster_php_1 cp .env.example .env
```

# To run migrations
```shell
docker exec -u root roster_php_1 php artisan migrate --force
docker exec -u root roster_php_1 chown www-data:www-data /tmp/laravel
```

# To run tests with coverage report

```shell
docker exec roster_php_1 php artisan test --coverage-html tests/reports/coverage
```

The report can be now viewed in tests/reports/index.html

# Quering the API

It is recommended to use the Insomnia software and import the request collection from attached insomnia_v4.json file

Alternatively you can manually scrap the `url` field from the json file which contains all urls and query params
