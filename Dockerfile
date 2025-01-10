# Используем официальный образ PHP с FPM
FROM php:8.1-fpm

# Устанавливаем необходимые зависимости для установки pdo_mysql
RUN apt-get update && \
    apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем файлы проекта в контейнер
COPY . .

# Настроим команду запуска контейнера
CMD ["php-fpm"]

# Открываем порты (если нужно)
EXPOSE 9000