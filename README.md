<h1>Rodar o projeto:</h1>

```
docker build . && ./vendor/bin/sail up
```


No shell do docker, será necessário rodar as migrations e as seeds com o comando:

```
php artisan migrate && php artisan db:seed
```
