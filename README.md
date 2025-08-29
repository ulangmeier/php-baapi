# Business Application API Router for PHP.
#### API-Routing for PHP.

Create an industry-standard API in PHP that follows best-practices, without any hazzle.

You do not have to switch to Express.js, Python or Go.
Do it like in Express.js, Python or Go, but in PHP.

This api router gives PHP developers an easy way to create any api route.

Instead of methods, we use simple if statements in our api router. You do not need complicated METHODS and frameworks!

If you don't believe me, just have a look:

Simple Route without parameter:
-------------------------------

**Hello World** API route:

```php
if (route("/hello", GET)) {
   echo json_encode(["message" => "Hello, World!"]);
}
```

Route with parameter:
---------------------

**Say *Hello* to a specific x-being**:

Note: $params is an array that contains the api parameter (here the value of the passed {name}).

```php
if (route("/hello/{name}", "GET", $params)) {
  echo json_encode(["message" => "Hello, ".$params[0]."!"]);
  exit;
}
```

**Getting an user id**:
```php
if (route("/user/{id}", GET, $params)) {
  echo json_encode(["user_id" => $params[0]]);
  exit;
}
```

A more advanced route could be:
-------------------------------

Creates a customer with the `vip` option:

```php
if ( route("/customer/create/{id}/{name}/as/vip", PUT, $params) ) {
  echo json_encode(["message" => "Customer " . $params[0] . " with Name ". $params[1]." was created as VIP."]);
  exit;
}
```


## How to test & call the routes:

Just use CURL from your commandline:
```bash
curl -X GET http://your-website.com/api/v1/hello/Urs+Langmeier -H "Authorization: Bearer your-secret-token"
```

If you need additional debug information use the -i option (shows the HTTP-Request Header):
```bash
curl -X GET https://your-server.name/api/v1/hello/Urs+Langmeier -H "Authorization: Bearer your-secret-token" -i
```


## How to define your own routes:
  
Please go to the routes.php file and define your API routes there.

Also you can place any other php file into this directory where you add
more routes for a specific topic.

For example `sms.php` for SMS specific API routes, or `language.php` for
the translation of language.

DON'T place other php files into this directory, because all php files that
you place into this folder will be executed to find the requtested API route.

See above some example routes, you find these routes also as pre-defined examples
in the routes.php file already.



## Installation

>Place your secret api token in the `.env` file! This is your bearer token for all api-calls.


### Adding the site to XAMPP for Windows

If you use XAMPP for Windows, you may use `xampp-create-vhost.bat` and start it with Admin privileges
to setup the test site in XAMPP.


### 📌 Apache

If you use Apache, the `.htaccess` file is already working in /api/v1/. No additional steps are necessairy to install and setup BAAPI.

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### 📌 Nginx

If you are using Nginx, you can define the forwarding with location blocks in your server configuration.

#### Nginx location block configuration

Add this block to `/etc/nginx/sites-available/default`:

```
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
```

**What’s happening here?**

- All requests under `/api/v1/` are forwarded to `index.php`.  
- PHP files are processed via `fastcgi_pass`.

1. **Restart Nginx:**

   ```bash
   sudo systemctl restart nginx
   ```

2. Call your api with `http://your-domain.com/api/v2/hello`


