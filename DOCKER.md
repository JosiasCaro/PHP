# Docker

## Descargar docker desktop y armar un directorio con carpeta docker y dentro 2 carpetas mas una que sera app (en esta una carpeta supervisor) y otra nginx, carpeta symfony con el proyecto, un README.md y docker-compose.yml

## Estructura del proyecto

*   `proyecto/`
    *   `docker/`
        *   `app/`
            * `supervisor/`
              * `supervisor.conf`
            * `Dockerfile`
        *   `nginx/`
            * `default.conf`
    *   `symfony/`
    *   `docker-compose.yml`
    *   `README.md`

## Archivo Symfony

### Creamos un proyecto symfony y lo cortamos en esta carpeta

## Archivo nginx

### Creamos un archivo "default.conf" con lo siguiente

~~~
server{
    listen 80;
    index index.php index.html;
    root /var/www/html/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass peligro-symfony-app:9000; //(peligro-symfony-app este es el nombre del contenedor)
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param APP_ENV local;
    }

    location ~ /\.ht {
        deny all;
    }
}
~~~

## Carperta supervisor

### Creamos "supervisor.conf" con lo siguiente:

~~~
[supervisord]
nodaemon=true

[program:queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work --tries=3
autostart=true
autorestart=true
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/queue-worker.log
~~~

## Archivo dockerfile en app

~~~
FROM php:8.3-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    nano \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libldap2-dev \
    supervisor \
    libssl-dev \
    && apt-get clean

# Instalar extensiones de PHP
#RUN docker-php-ext-install pdo_mysql mbstring zip gd
RUN docker-php-ext-configure ldap --with-libdir=/lib/x86_64-linux-gnu \
    && docker-php-ext-install pdo_mysql mbstring zip gd ldap 

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copiar configuración de Supervisor
COPY supervisor/supervisor.conf /etc/supervisor/supervisord.conf


# Configurar el directorio de trabajo
WORKDIR /var/www/html
~~~

## Archivo docker-compose.yml

### Configuracion del archivo

~~~
version: '3.8'

services:
  app:
    build:
      context: ./docker/app //Busca en esta direccion la configuracion del dockerfile
    container_name: peligro-symfony-app //nombre del contenedor
    working_dir: /var/www/html //donde trabaja internamente la aplicacion
    volumes:
      - ./symfony:/var/www/html // toma todo lo de la carpeta symfony y lo lleva al contenedor en la carpeta de working lo que esta antes del : es lo que copia y despues a donde lo copia
    environment:
      - APP_ENV=local
      - APP_DEBUG=true
      - APP_KEY=base64:K0QkoJdjC83TX5sJ76/O1XLzp5nN+G6XG8uhxdjBzQM=
    ports:
      - "8000:8000" //puerto configuracion del contenedor (no es por el que salimos)
    networks:
      - custom_network 
    

  nginx:
    image: nginx:alpine  //imagen mas comun para este tipo de situaciones
    container_name: symfony-nginx
    volumes:
      - ./symfony:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    ports:
      - "8080:80" //Puertos de salida
    depends_on:
      - app
    networks:
      - custom_network
  


networks:
  custom_network:  //nombre de la network
    driver: bridge
    ipam:
      config:
        - subnet: 172.42.12.0/28  //configuramos para aumentar el rango de ips, evitando problemas con las vpn
          gateway: 172.42.12.1
~~~

### Para verificar si tengo un contenedor activo uso "docker image ls" parados en la carpeta de todo el proyecto.

### Para levantar el contenedor uso "docker compose up --build" o "docker-compose up --build" (queda la terminal tomada y el contenedor levantado)

### Para levantar el contenedor uso "docker compose up --build -d" (la terminal no queda tomada y el contenedor levantado)

### Para ver los contenedores activos "docker ps" o "docker container ls"

### Para entrar a la terminal del contenedor "docker exec -it {nombreContenedor} php -v" (con esto veo la version de php del contenedor)

### Para entrar a la terminal del contenedor "docker exec -it {nombreContenedor} composer install" (con esto creo la carperta vendor)

### Para entrar al contenedor "docker exec -it {nombreContenedor} bash"

### Instalar Doctrine ORM "composer require symfony/orm-pack" y ponogo que no quiero la imagen de docker e instalo "composer require --dev symfony/maker-bundle" voy al .env y configuro la base de datos con los datos correspondientes descomentando la bdd que usare

### Para crear la bdd uso "php bin/console doctrine:database:create", si la configuracion de env es correcta me deja crear la base de datos y hacer las migraciones y demas.

### Detener el contenedor "docker compose down" y baja todos los contenedores. Para eliminar imagenes "docker system prune --all" (borra todos).