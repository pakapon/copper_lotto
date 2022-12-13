<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
use \Firebase\JWT\JWT;

$email = '';
$password = '';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$refresh_token = $data->refresh_token; 

try {
    $decoded = JWT::decode($refresh_token, $secret_key_refresh, array('HS256'));
} catch (\Firebase\JWT\ExpiredException $exception) {
    http_response_code(401);

    echo json_encode(array(
        "message" => "Access denied.",
        "error" => "Authorization timeout."
    ));
    $access = false;
    exit();
}

$query = "  SELECT *  FROM   " . $table_aut . " a 
            JOIN " . $table_us . " b ON a.user_code = b.user_code
            WHERE refresh_token = ? AND status = 1";

$stmt = $conn->prepare($query);
$stmt->bindParam(1, $refresh_token);
$stmt->execute();
$num = $stmt->rowCount();

if ($num > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $date1=date_create($row['create']);
    $date2=date_create(date("Y-m-d H:i:s"));
    $diff=date_diff($date1,$date2); 
    if( $diff->format("%a") > 1 AND $row['user_type'] == 2){  
        http_response_code(401);
        echo json_encode(array("message" => "Refresh token failed.",
        "error"   => "Login over 1 day."));
    }

    $no = $row['no'];
    $id = $row['user_code'];
    $firstname = $row['user_name']; 
    $role = $row['user_type'];
    $password2 = $row['user_password'];
    $refcode = $row['user_refcode'];

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

    http_response_code(200);

    $jwt = JWT::encode($token, $secret_key);
    echo json_encode(
        array(
            "message" => "Successful refresh.",
            "token_type" => "Bearer",
            "token" => $jwt,
            "email" => $email,
            "expireAt" => $expire_claim
        )
    );

    try {

        $query_up = "UPDATE " . $table_aut . " SET `token` = ? , `update` = now() WHERE `no` = ? ";

        $stmt_up = $conn->prepare($query_up);
        $stmt_up->bindParam(1, $jwt);
        $stmt_up->bindParam(2, $no);
        $stmt_up->execute();
    } catch (Exception $q) {
        http_response_code(401);
        echo json_encode(array("message" => "Refresh token wrong." . $q));
    }
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Refresh token failed."));
}
