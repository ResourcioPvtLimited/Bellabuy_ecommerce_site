<?php

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);


// Handle CORS if needed
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: api_key, secret_key, Content-Type");

// Handle preflight request for CORS
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit();
}

require "../config.php";
if ($conn->connect_error) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$api_key = "a";
$secret_key = "b";
$headers = getallheaders();

if (
    !isset($headers["api_key"]) ||
    $headers["api_key"] !== $api_key ||
    !isset($headers["secret_key"]) ||
    $headers["secret_key"] !== $secret_key
) {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(["message" => "KEY_ERROR"]);
    exit();
}

$allowedTables = [
    "banner",
    "cart",
    "cat",
    "coupon",
    "cust",
    "history",
    "item",
    "news",
    "orders",
    "review",
    "shop",
    "wishlist",
];

$table = isset($_GET["table"]) ? $_GET["table"] : null;

if (!$table || !in_array($table, $allowedTables)) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(["message" => "TABLE_ERROR"]);
    exit();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];
$data = json_decode(file_get_contents("php://input"), true);

switch ($requestMethod) {
    case "GET":
        $conditionCol = isset($_GET["conditionCol"]) ? $_GET["conditionCol"] : null;
        $conditionVal = isset($_GET["conditionVal"]) ? $_GET["conditionVal"] : null;

        if ($conditionCol && $conditionVal) {
            // Sanitize column name to avoid SQL injection
            $conditionCol = preg_replace("/[^a-zA-Z0-9_]/", "", $conditionCol);
            $query = "SELECT * FROM `$table` WHERE `$conditionCol` = '" . $conn->real_escape_string($conditionVal) . "'";
        } else {
            $query = "SELECT * FROM `$table`";
        }

        $result = $conn->query($query);
        if ($result === false) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["error" => "Query failed: " . $conn->error]);
        } else {
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($rows);
        }
        break;

    case "POST":
        $action = isset($_POST["action"]) ? $_POST["action"] : null;
       
            if (!$data) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["error" => "Data parameter is missing"]);
                exit();
            }

          // Convert values to string with proper escaping and quoting
$columns = implode(", ", array_keys($data));
$escapedValues = array_map(function($value) use ($conn) {
    return "'" . $conn->real_escape_string($value) . "'";
}, array_values($data));
$values = implode(", ", $escapedValues);

$query = "INSERT INTO `$table` ($columns) VALUES ($values)";

            if ($conn->query($query) === TRUE) {
                echo json_encode(["message" => "INSERT_SUCCESS"]);
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode(["error" => "Insert failed: " . $conn->error]);
            }
      
        break;

    case "PUT":
        $conditionCol = isset($_GET["conditionCol"]) ? $_GET["conditionCol"] : null;
        $conditionVal = isset($_GET["conditionVal"]) ? $_GET["conditionVal"] : null;

        if (!$conditionCol || !$conditionVal || !$data) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Condition column, value, or data parameter is missing"]);
            exit();
        }

        // Sanitize column name to avoid SQL injection
        $conditionCol = preg_replace("/[^a-zA-Z0-9_]/", "", $conditionCol);

        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "`$key` = '" . $conn->real_escape_string($value) . "'";
        }
        $setClause = implode(", ", $setParts);
        $query = "UPDATE `$table` SET $setClause WHERE `$conditionCol` = '" . $conn->real_escape_string($conditionVal) . "'";

        if ($conn->query($query) === TRUE) {
            echo json_encode(["message" => "UPDATE_SUCCESS"]);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["error" => "Update failed: " . $conn->error]);
        }
        break;

    case "DELETE":
        $conditionCol = isset($_GET["conditionCol"]) ? $_GET["conditionCol"] : null;
        $conditionVal = isset($_GET["conditionVal"]) ? $_GET["conditionVal"] : null;

        if (!$conditionCol || !$conditionVal) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Condition column or value parameter is missing"]);
            exit();
        }

        // Sanitize column name to avoid SQL injection
        $conditionCol = preg_replace("/[^a-zA-Z0-9_]/", "", $conditionCol);

        $query = "DELETE FROM `$table` WHERE `$conditionCol` = '" . $conn->real_escape_string($conditionVal) . "'";

        if ($conn->query($query) === TRUE) {
            echo json_encode(["message" => "DELETE_SUCCESS"]);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["error" => "Delete failed: " . $conn->error]);
        }
        break;

    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["error" => "Invalid request method"]);
}























$conn->close();
?>
