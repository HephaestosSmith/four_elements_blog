FROM php:8-apache

# 安裝 mysqli 和其他必要的擴展
RUN docker-php-ext-install mysqli

# 啟用 Apache mod_rewrite
RUN a2enmod rewrite

# 設置 Apache 文檔根目錄
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
