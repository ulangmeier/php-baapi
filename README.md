#1️⃣ Apache (empfohlen) – .htaccess für sauberes Routing

Falls du Apache nutzt, kannst du eine .htaccess-Datei in deinem /api/v2/ Ordner erstellen, um alle Anfragen an eine zentrale index.php-Datei zu leiten.

## 📌 .htaccess in /api/v2/

```RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

# 2️⃣ Nginx – location Block für Weiterleitung

Falls du Nginx nutzt, dann kannst du die Weiterleitung mit location-Blöcken in deiner Server-Konfiguration definieren.

## 📌 Nginx Config (/etc/nginx/sites-available/default)

server {
    listen 80;
    server_name yourdomain.com;

    location /api/v2/ {
        rewrite ^/api/v2/(.*)$ /api/v2/index.php last;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;  # Anpassen je nach PHP-Version
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}


#🔹 Was passiert hier?

Alle Anfragen unter `/api/v2/` werden auf index.php weitergeleitet.
PHP-Dateien werden über fastcgi_pass verarbeitet.
Danach:

Nginx neu starten mit sudo systemctl restart nginx
API unter http://yourdomain.com/api/v2/hello aufrufen
