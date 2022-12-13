<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config/globalfuction.php';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$user_code          = strtoupper(uniqid()); 
$user_type          = 1;     //  0 = user , 1 = agnent , 2 = admin , 4 = staff 
$user_tel           = $data->user_tel;
$user_password      = $data->user_password;
$user_name          = $data->user_name;
$user_address       = $data->user_address;
$user_address_id    = $data->user_address_id;
$user_status        = 1;                    //  0  = inactive , 1 = active  

if (empty($user_password) ) {
    http_response_code(400);
    echo json_encode(array("message" => "Must Input Password  .", "error" => ""));
    exit();
} 

$check_lineconn = "SELECT * FROM " . $table_us . " WHERE user_tel = '" . $user_tel . "'";
$stmt_lineconn = $conn->prepare($check_lineconn);
$stmt_lineconn->execute() or die(json_encode(
    array(
        "message"   => "execute not success.",
        "error"     => $stmt_lineconn->errorInfo()
    ),
    http_response_code(400)
));
$num = $stmt_lineconn->rowCount();
$val = $stmt_lineconn->fetch(PDO::FETCH_ASSOC);
$o_tel  = $val["user_tel"];
 
if ($num > 0) { 
    http_response_code(400);
    echo json_encode(array("message" => "Phone had already.", "error" => ""));
    exit();
} else {  
      
    $query = "INSERT INTO " . $table_us . "
        SET
            user_code           = :user_code,
            user_tel            = :user_tel,
            user_password       = :user_password,
            user_type           = :user_type,
            user_name           = :user_name,
            user_address        = :user_address,
            user_address_id     = :user_address_id,
            user_status         = :user_status,
            user_update         = now(),
            user_create         = now()
            ";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':user_code',      $user_code);
    $stmt->bindParam(':user_tel',       $user_tel);
    $stmt->bindParam(':user_type',      $user_type);
    $stmt->bindParam(':user_name',      $user_name);
    $stmt->bindParam(':user_address',   $user_address);
    $stmt->bindParam(':user_address_id',$user_address_id);
    $stmt->bindParam(':user_status',    $user_status);

    $password_hash = password_hash($user_password, PASSWORD_BCRYPT);
    $stmt->bindParam(':user_password', $password_hash);

    $stmt->execute() or die(json_encode(
        array(
            "message"   => "execute not success.",
            "error"     => $stmt->errorInfo()
        ),
        http_response_code(400)
    ));
} 

http_response_code(201);
echo json_encode(array("message" => "successfully.", "error" => ""));
