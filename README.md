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
docker exec roster_php_1 php artisan migrate --force
```

# To run tests with coverage report

```shell
docker exec roster_php_1 php artisan test --coverage-html tests/reports/coverage
```

The report can be now viewed in tests/reports/index.html

