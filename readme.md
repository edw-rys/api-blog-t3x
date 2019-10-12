# t3x Blog

Blog de prueba usando Laravel.

Framework usado para el api: `Laravel 5.7.*`.

## Características Básicas

- Usuarios: registro, actualización, borrado y consulta.
- Categoría de posts: inserción, actualización, borrado y consulta.
- Posts: inserción, actualización, borrado y consulta.


## Server Requirements

- PHP >= 7.1.0
- Node >= 6.x
- PDO PHP Extension

## Install

### 1. Clone the source code or create new project.

```shell
git clone https://github.com/edw-rys/api-blog-t3x.git
```

### 2. Set the basic config

```shell
cp .env.example .env
```

Edite el `.env` archivo y configure la  `base de datos` otra configuración para el sistema después de copiar el `.env`.

### 2. Install the extended package dependency.

Instalar las dependencias de `Laravel`: 

```shell
composer install -vvv
```

### 3. Ejecute el comando de instalación del blog, el comando ejecutará el migratecomando y generará datos de prueba.

```shell
php artisan blog:install
```


### 4. Conectar con el Frontend
- [Apicación del blog hecho en angular](https://github.com/edw-rys/app-blog-t3x)

## Contributors

- [Edward Reyes](http://https://github.com/edw-rys)


## License
El proyecto es un software de código abierto.

