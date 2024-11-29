## Al-Umm

Sistem Induk Ma'had Al-Imam Asy-Syathiby

## Installation

This project based of a TALL stack admin panel starter kit. This started can be used with Laravel 8+, Laravel Livewire 2.5.4+ and AlpineJS 3+.

```
git clone https://github.com/mithicher/laravel-fresh.git

composer install

npm install && npm run dev

php artisan storage:link
```

Configure the database with your credentials

```
php artisan migrate --seed
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
