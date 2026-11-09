FROM php:7.2-fpm

# Diretório de trabalho padrão no container
WORKDIR /var/www/html

# Instalar dependências de sistema necessárias para compilação e suporte ao Laravel 7
RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath pcntl xml tokenizer ctype json \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar o Composer 2.2 LTS (versão que mantém suporte ativo para PHP 7.2)
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Configuração de usuário não-root com UID/GID para compatibilidade com WSL/Linux
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Copiar arquivos de configuração customizada do PHP
COPY ./docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Copiar arquivos da aplicação
COPY . /var/www/html
COPY --chown=www:www . /var/www/html

# Usuário de execução padrão
USER www

EXPOSE 9000
CMD ["php-fpm"]
