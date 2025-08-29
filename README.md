## Best & simple PHP API Router
# API-Routing für PHP. Code any API in PHP with less effort.

You do not have to switch to Node, Express.js, Python or Go to create a industry-standard API routing.

Just use the open source framework php-baapi to create easy API routes that other developers love in other programming languages.

Instead of methods, we use simple if statements.

## How to define routes:
  
  Please go to the routes.php file and define your API routes there.
  
  Also you can place any other php file into this directory where you add
  more routes for a specific topic.

  For example `sms.php` for SMS specific API routes, or `language.php` for
  the translation of language.
  
  DON'T place other php files into this directory, because all php files that
  you place into this folder will be executed to find the requtested API route.
  
  Here is an example of how to define routes in the routes.php file:
  
  Simple Route without parameter:
  -------------------------------
  
  if (route("/hello", GET)) {
     echo json_encode(["message" => "Hello, World!"]);
  }
  
  Route with parameter:
  ---------------------
  
  if (route("/user/{id}", GET, $arParams)) {
     // $arParams is an array that contains the api parameter (here the value of the passed-over {id}).
    echo json_encode(["user_id" => $arParams[0]]);
    exit;
  }

  A more advanced route could be:
  -------------------------------

  if ( route("/customer/create/{id}/{name}/as/gay", PUT, $arParams) ) {
    // Creates a customer with the `gay` option.
    echo json_encode(["message" => "Customer " . $arParams[0] . " with Name ". $arParams[1]." was created as gay."]);
    exit;
  }


## How to test & call the routes:

  Just use CURL from your commandline:
  curl -X GET http://your-website.com/api/v2/hello/Urs+Langmeier -H "Authorization: Bearer your-secret-token"


## Installation

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

    ```server {
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
    ```
    
    #🔹 Was passiert hier?
    
    Alle Anfragen unter `/api/v2/` werden auf index.php weitergeleitet.
    PHP-Dateien werden über fastcgi_pass verarbeitet.
    Danach:
    
    Nginx neu starten mit sudo systemctl restart nginx
    API unter http://yourdomain.com/api/v2/hello aufrufen

# Adding the site to XAMPP for Windows

If you use XAMPP for Windows, you may use `xampp-create-vhost.bat` and start it with Admin privileges
to setup the test site in XAMPP.
