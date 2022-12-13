<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use \Firebase\JWT\JWT;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$phone          = $data->phone;
$password       = $data->password;
$ip             = $_SERVER['HTTP_X_REAL_IP'];
$browser        = $_SERVER['HTTP_SEC_CH_UA'];
$device         = $_SERVER['HTTP_USER_AGENT'];

$query = "SELECT * FROM " . $table_us . " WHERE user_tel = ? LIMIT 1 ;";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $phone);

$stmt->execute();
$num = $stmt->rowCount();

if ($num > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $row['user_code'];
    $firstname = $row['user_name'];
    $lastname = $row['user_lastname'];
    $role = $row['user_type'];
    $password2 = $row['user_password'];
    $password2 = $row['user_status'];
    $true_pass = password_verify($password, $password2);  

    $issuer_claim = "API_COUPLE"; // this can be the servername
    $audience_claim = "API_COUPLE";
    $issuedat_claim = time(); // issued at
    $notbefore_claim = $issuedat_claim + 1; //not before in seconds
    $expire_claim = $issuedat_claim + $expiretimesec; // expire time in seconds

    $token = array(
        "iss" => $issuer_claim,
        "aud" => $audience_claim,
        "iat" => $issuedat_claim,
        "nbf" => $notbefore_claim,
        "exp" => $expire_claim,
        "data" => array(
            "id"                => $id,
            "firstname"         => $firstname,
            'role'              => $role,
        )
    );

    $token_refresh = array(
        "iss" => $issuer_claim . "_refresh",
        "aud" => $audience_claim . "_refresh",
        "iat" => $issuedat_claim,
        "nbf" => $notbefore_claim,
        "exp" => $expire_claim + $expiretimesec_refresh,
        "data" => array(
            "id"        => $id,
            "firstname" => $firstname,
            "role"      => $role,
        )
    );

    http_response_code(200);

    $jwt            = JWT::encode($token, $secret_key);
    $jwt_refresh    = JWT::encode($token_refresh, $secret_key_refresh);

    try {

        $query_up = "INSERT INTO " . $table_aut . " SET 
                                `user_code`       = ? ,
                                `token`           = ? ,
                                `refresh_token`   = ? ,
                                `ip`              = ? ,
                                `browser`         = ? ,
                                `device`          = ? ,
                                `scanner`         = ? ,
                                `mac_address`     = ? ,
                                `status`          = 1 ,
                                `create`          = now() , 
                                `update`          = now() 
                            ";

        $stmt_up = $conn->prepare($query_up);
        $stmt_up->bindParam(1, $id);
        $stmt_up->bindParam(2, $jwt);
        $stmt_up->bindParam(3, $jwt_refresh);
        $stmt_up->bindParam(4, $ip);
        $stmt_up->bindParam(5, $browser);
        $stmt_up->bindParam(6, $device);
        $stmt_up->bindParam(7, $admincode);
        $stmt_up->bindParam(8, $mac_address);

        $stmt_up->execute();

        echo json_encode(
            array(
                "message" => "Successful login.",
                "token_type" => "Bearer",
                "token" => $jwt,
                "refresh_token" => $jwt_refresh,
                "email" => $email,
                "expireAt" => $expire_claim
            )
        );
        http_response_code(200);
    } catch (PDOStatement $q) {
        http_response_code(401);
        echo json_encode(array("message" => "Login failed somthing wrong ", "error" => $q));
    }
} else {
    http_response_code(401);
    echo json_encode(array(
        "message" => "Login failed user wrong ",
        "error"   => "User denied.",
    ));
}
