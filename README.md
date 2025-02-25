## Al-Umm

Sistem Induk Ma'had Al-Imam Asy-Syathiby

## Installation

This project based of a TALL stack admin panel starter kit. This started can be used with Laravel 8+, Laravel Livewire 2.5.4+ and AlpineJS 3+.

```
git clone https://github.com/CreatorB/al-umm.git

cd al-umm

composer install

npm install && npm run dev

php artisan storage:link
```

Configure the database with your credentials

```
php artisan migrate --seed
```

** Dev update, adding title to schedules table**
```
composer require doctrine/dbal

php artisan migrate --path=/database/migrations/2025_02_23_000001_add_title_to_schedules_table.php

php artisan schedules:update-titles
```

Useful commands in development

```
// check current links
php artisan route:list
php artisan route:list --method=GET
php artisan route:list --name=user
php artisan route:list --json

php artisan view:clear && php artisan optimize:clear && rm -rf storage/framework/views/*

// if you have adding new packages, better to run below command again :
composer install ; composer dump-autoload
// or composer update --lock
```

## Features:

- Reusable Blade Components
- Custom Error Templates
- Filepond Uploader
- Flatpicker
- Masked Input

## Plugins & Libraries Used:

- [Laravel Breeze](https://laravel.com/docs/8.x/starter-kits#laravel-breeze)
- [Laravel Livewire](https://laravel-livewire.com/)
- [Laravel Permissions](https://spatie.be/docs/laravel-permission/v4/introduction)
- [Tailwind CSS](https://tailwindcss.com/)
- [Alpine.JS](https://alpinejs.dev/)

## Hosting

```
composer install --optimize-autoloader --no-dev

php artisan config:cache
php artisan route:cache
php artisan view:cache

chmod -R 775 storage
chmod -R 775 bootstrap/cache

ln -s ../storage/app/public public/storage

#optional
chmod -R 755 /path/to/your/laravel/public
find /path/to/your/laravel/public -type f -exec chmod 644 {} \;

```

.htaccess @ public directory
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
</IfModule>
```

.htaccess @ root directory
```
# unindex search engine
User-agent: *
Disallow: /
# Redirect all requests to the /public folder
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^$ public/ [L]
    RewriteRule (.*) public/$1 [L]
</IfModule>
```

### Another hosting solution

---
## **🔧 Solution 1: Use the `--no-scripts` Option**  
Since the error occurs when running **`post-autoload-dump`**, try installing Composer without executing automatic scripts:

```sh
composer install --no-scripts
```

Then, manually run the following commands to complete the installation:

```sh
php artisan clear-compiled
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
```

If your application runs correctly, then the issue is only with Composer scripts.

---

## **🔧 Solution 2: Use Composer PHAR**  
Try using **Composer PHAR**, as the installed Composer version on the server might have a bug or be incompatible with PHP 8.3.

1. **Download Composer PHAR on your hosting server**:  
   ```sh
   curl -sS https://getcomposer.org/installer | php
   ```

2. **Use Composer PHAR to install dependencies**:  
   ```sh
   php composer.phar install --no-dev --optimize-autoloader
   ```

If this works, **remove the Composer installed via cPanel** and use `composer.phar` as the default.

---

## **🔧 Solution 3: Install Locally and Upload to Hosting**  
Since shared hosting restricts some PHP functions, you can install dependencies **on your local machine** and then upload the `vendor/` folder to the hosting server.

1. **On your local machine**, run:  
   ```sh
   composer install --no-dev --optimize-autoloader
   ```

2. **Upload the entire `vendor/` folder** to **`public_html/al-umm/vendor/`** via **FTP** or **cPanel File Manager**.

3. **Ensure the `.env` file and other configuration files are correctly set** on the hosting server.

---

## Dockering

### initial setup

```
docker run -d -p 8001:8000 -p 8003:80 -p 8005:3306 -v /home/creatorbe/dev:/var/www/html/dev --name dev lampn

docker exec -it dev bash

composer install

npm install

cp .env.example .env

php artisan key:generate

php artisan migrate --seed

php artisan serve --host=0.0.0.0

npm run dev
```

### sql setup

```
CREATE USER 'root'@'172.17.0.1' IDENTIFIED BY '';

GRANT ALL PRIVILEGES ON *.* TO 'root'@'172.17.0.1';

FLUSH PRIVILEGES;

CREATE DATABASE al_umm;
```

## Contributing

We welcome contributions from the community to help improve Syathiby Mail. To contribute, please follow these steps:

1. **Fork the Repository**:
   - Click the "Fork" button at the top right of the repository page.

2. **Clone Your Fork**:
   ```bash
   git clone https://github.com/CreatorB/al-umm.git
   cd al-umm
   ```

3. **Create a New Branch**:
   ```bash
   git checkout -b feature/your-feature-name
   ```

4. **Make Your Changes**:
   - Implement your feature or bug fix.
   - Ensure that your code follows the project's coding standards.

5. **Commit Your Changes**:
   ```bash
   git commit -m "Add your commit message here"
   ```

6. **Push to Your Fork**:
   ```bash
   git push origin feature/your-feature-name
   ```

7. **Create a Pull Request**:
   - Go to the original repository and click the "New Pull Request" button.
   - Select your branch and submit the pull request.

## License

It is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
