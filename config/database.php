<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
   header("HTTP/1.1 200 OK");
   return;
}

header("Access-Control-Allow-Origin: * ");
header("Content-Type: applicationjson; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 60");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Expose-Headers: Content-Length, X-JSON");
header("Access-Control-Allow-Headers: *");

// // off error
ini_set('log_errors', 'On');
ini_set('display_errors', 'off'); //
ini_set('error_reporting', E_ALL);
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', false); //false
define('WP_DEBUG_DISPLAY', true);

include "tablelist.php";

$secret_key = "themoonchildpakaponls";  // key
$secret_key_refresh = "moonchildpakaponlss";  // key
$expiretimesec = 86400; // sec 
$expiretimesec_refresh = 604800; // sec

// PRO SERVER
$websiteUrl = "http://phpstack-895265-3106351.cloudwaysapps.com/";
$websiteUrl_ns = "http://phpstack-895265-3106351.cloudwaysapps.com";



// used to get mysql database connection
class DatabaseService
{

    private $db_host = "127.0.0.1";   
    private $db_name = "bytxhcqwmr";
    private $db_user = "bytxhcqwmr";
    private $db_password = "tmjkDkH54N"; 

    private $connection;

    public function getConnection()
    {

        $this->connection = null;

        try {
            $this->connection = new PDO("mysql:host=" . $this->db_host . ";dbname=" . $this->db_name, $this->db_user, $this->db_password);
        } catch (PDOException $exception) {
            echo "Connection failed: " . $exception->getMessage();
        }

        return $this->connection;
    }
}


$service = new DatabaseService();
$servicecon = $service->getConnection();

$jsonlog = file_get_contents("php://input");
$data2_log = json_encode($_SERVER);

$query_logg = "INSERT INTO `" . $table_slog . "`
                SET `log`    = :data,
                    `body`  = :body,
                    `create`  =now()
                    ;";

$stmt_log = $servicecon->prepare($query_logg);
$stmt_log->bindParam(':data', $data2_log);
$stmt_log->bindParam(':body', $jsonlog);
$stmt_log->execute() or die(json_encode(
    array(
        "message"   => "execute not success.",
        "error"     => $stmt_log->errorInfo()
    ),
    http_response_code(400)
));