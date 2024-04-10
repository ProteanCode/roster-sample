# To start the project (takes port 80)

```shell
cd .docker
docker-compose -p roster up
```

# Install dependencies
```shell
docker exec roster_php_1 composer install
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

