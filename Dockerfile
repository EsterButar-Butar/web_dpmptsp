FROM php:8.2-apache

# 1. Install PostgreSQL PDO extensions (untuk koneksi ke Supabase)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# 2. Ubah Apache DocumentRoot ke direktori public milik Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 3. Aktifkan modul mod_rewrite Apache (untuk routing Laravel)
RUN a2enmod rewrite

# 4. Tentukan folder kerja di dalam container
WORKDIR /var/www/html

# 5. Salin seluruh file proyek ke dalam container
COPY . .

# 6. Install Composer & dependensi Laravel (tanpa paket development)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 7. Berikan izin akses folder storage & bootstrap cache agar Laravel bisa menulis log/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Sesuaikan port Apache ke 8080 (karena Google Cloud Run menggunakan port default 8080)
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf
EXPOSE 8080

# 9. Jalankan Apache di foreground
CMD ["apache2-foreground"]
