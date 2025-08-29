<?php
 /* Testcase:
    ==========

    # Say hello to Urs:
    curl -X GET https://your-server.name/api/v2/hello/Urs+Langmeier -H "Authorization: Bearer your-secret-token"

    # If you need additional debug information use the -i option (shows the HTTP-Request Header):
    curl -X GET https://your-server.name/api/v2/hello/Urs+Langmeier -H "Authorization: Bearer your-secret-token" -i

*/

/** Say hello to the world 
*/
if (route("/hello", "GET")) {
    echo json_encode(["message" => "Hello World!"]);
    exit;
}

/**
 * Say hello to a specific person.
 *
 * use: /hello/{name}
 */
if (route("/hello/{name}", "GET", $params)) {
    echo json_encode(["message" => "Hello, $params[0]!"]);
    exit;
}

/**
 * A more advanced route could be:
 */
if ( route("/customer/create/{id}/{name}/as/vip", PUT, $params) ) {
    echo json_encode(["message" => "Customer $params[0] with Name $params[1] was created as VIP."]);
    exit;
}

/**
 * Echoes the received JSON data back to the client.
 * For testing purposes only.
 */
if (route("/echo", POST)) {

    $json_input = file_get_contents("php://input");
    $data = json_decode($json_input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid JSON"]);
        exit;
    }

    echo json_encode(["received" => $data]);
    exit;
}