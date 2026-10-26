FROM php:8.3-apache

# 1. Install system dependencies & build tools
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libcurl4-openssl-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# 2. Configure & install PHP 8.3 extensions required by Mali Setu (Laravel 12, DomPDF, Excel, Scout, Razorpay, SMTP Mail)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        curl \
        opcache

# 3. Copy custom PHP 8.3 configuration
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# 4. Copy Apache VirtualHost configuration
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# 5. Enable Apache rewrite & headers modules for Laravel .htaccess routing
RUN a2enmod rewrite headers

# 6. Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 7. Set working directory
WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
