<?php
/*
 * baapi.php
 * ***********
 * 
 * Business Application API Router for PHP.
 * 
 * Goal: Providing a good api router for every kind of Business by using API best practices.
 * 
 * 
 * How to define routes:
 * *************************
 * 
 * Please go to the routes.php file and define your API routes there.
 * Also you can place any file into this directory with additional routes.
 * These routes will be included automatically.
 * 
 * DON'T place other code into this directory, because it will be executed
 * on each API call.
 * 
 * Here is an example of how to define routes in the routes.php file:
 * 
 * Simple Route without parameter:
 * -------------------------------
 * 
 * if (route("/hello", GET)) {
 *    echo json_encode(["message" => "Hello, World!"]);
 * }
 * 
 * Route with parameter:
 * ---------------------
 * 
 * if (route("/user/{id}", GET, $params)) {
 *    // $params now contains the value of the parameter "id"
 *   echo json_encode(["user_id" => $params[0]]);
 *   exit;
 * }
 * 
 * 
 * @example
 * 
 * curl -X GET http://langmeier.abusiness.local/api/v2/hello/Urs+Langmeier -H "Authorization: Bearer secret-token"
 * 
 * 
 * Author: ula
 * Date: 12.02.2025
 * 
 * MIT License
 * 
 * Copyright (c) 2025 Urs Langmeier
 *
 * Published from Urs Langmeier, Langmeier Software, under the MIT License and for the
 * benefit of the community.
 *
*/

// Reading the .env file for configuration
if (file_exists(__DIR__ . "/../.env")) {
    $lines = file(__DIR__ . "/../.env", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = array_map('trim', explode('=', $line, 2));
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "Internal Server Error: Missing .env file."]);
    exit;
}

// Getting the secret API key from the environment:
define('SECRET_API_TOKEN', $_ENV["BAAPI_SECRET_KEY"]);

$gAccessError = null;
$gMatchedRoute = null;
$gMatchedMethod = null;


// Eine Shutdown-Funktion fängt Routen ab, die noch nicht definiert wurden
register_shutdown_function(function() {
    global $gMatchedRoute;
    global $gAccessError;
    if ( $gMatchedRoute === null && $gAccessError === null ) {
        http_response_code(404);
        echo json_encode(["error" => "API Endpoint not Found"]);
    }
});

// HTTP-Methoden als Konstanten definieren
const GET = "GET";
const POST = "POST";
const PUT = "PUT";
const DELETE = "DELETE";
const PATCH = "PATCH";
const OPTIONS = "OPTIONS";

function authenticate() {
    // Angenommene Authentifizierung (z.B. Token im Header)
    global $gAccessError;

    $headers = apache_request_headers();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        $gAccessError = "Unauthorized";
        exit;
    }

    if (!isset($headers['Authorization']) || $headers['Authorization'] !== 'Bearer '.SECRET_API_TOKEN) {
        http_response_code(403);
        echo json_encode(["error" => "Forbidden"]);
        $gAccessError = "Forbidden";
        exit;
    }
}

// Starte die Authentifizierung
authenticate();

// Route-Funktion für saubere API-Routen
function easyRoute($path, $method) {
    return trim($_SERVER['REQUEST_URI'], '/') === trim($path, '/') && $_SERVER['REQUEST_METHOD'] === $method;
}

// Route-Funktion für die Routenprüfung
/**
 * Fragt ab, ob die Route vom Caller aufgerufen wurde.
 * 
 * @example
 * 
 * # Einfache Route ohne Parameter:
 * 
 * if (route("/hello", GET)) {
 *    echo json_encode(["message" => "Hello, World!"]);
 * }
 * 
 * # Route mit Parameter:
 * 
 * if (route("/user/{id}", GET, $params)) {
 *    // $params enthält jetzt den Wert des Parameters "id"
 *   echo json_encode(["user_id" => $params[0]]);
 *   exit;
 * }
 *
 * @param  string $path     Der Pfad der Route
 * @param  string $method   Die HTTP-Methode, z.B. GET, POST, PUT, DELETE
 * @param  array  $params   Ein Array, in das die Parameter geschrieben werden
 * @return string 
 * @author Urs Langmeier
 */
function route($path, $method, &$params = []) {
    global $gMatchedRoute;
    global $gMatchedMethod;

    $path = trim($path, '/');
    $uri = trim($_SERVER['REQUEST_URI'], '/');

    // Entferne /api/v2/ von der URI (nur wenn am Anfang):
    $uri = preg_replace('/^api\//', '', $uri);
    // v2 v3 v4 etc.. vom Pfad entfernen:
    $uri = preg_replace('/^v[0-9]+\//', '', $uri);
    
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        return false;
    }

    // URL-Parameter im Pfad extrahieren
    $path_parts = explode('/', $path);
    $uri_parts = explode('/', $uri);

    if (count($path_parts) !== count($uri_parts)) {
        // Die Anzahl der Teile im Pfad und in der URI müssen übereinstimmen
        // ->Raus aus dieser Route...
        // debug:
        // echo 'wrong method: '.$path."[".$method."], because: ".$uri;
        return false;
    }

    foreach ($path_parts as $i => $part) {
        if (strpos($part, '{') === false) {
            // Wenn der Teil des Pfades kein Parameter ist, muss er übereinstimmen
            if ($part !== $uri_parts[$i]) {
                return false;
            }
        } else {
            // Extrahieren des Parameters und in $params speichern
            $params[] = urldecode($uri_parts[$i]);
        }
    }

    $gMatchedRoute = $path;
    $gMatchedMethod = $method;
    
    return true;
}

// JSON-Header setzen
header("Content-Type: application/json");

// Now include the routes.php file to define the routes:
require_once("routes.php");

// If there are other files in the same directory, include them here:
// Loop through all files in the directory and include them
$files = scandir(__DIR__);
foreach ($files as $file) {
    if (is_file(__DIR__ . "/" . $file) && $file !== 'baapi.php' && $file !== 'routes.php') {
        require_once($file);
    }
}

