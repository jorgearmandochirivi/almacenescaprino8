# php/Dockerfile
FROM php:8.5-fpm
RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
		libzip-dev \
		libonig-dev \
		libxml2-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install mysqli mbstring dom gd zip \
	&& docker-php-ext-enable mysqli mbstring dom gd zip \
	&& rm -rf /var/lib/apt/lists/*
WORKDIR /var/www/html