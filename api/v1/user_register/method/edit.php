<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config/globalfuction.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/api/v1/protected.php';

if ($role_name != 2) {
    http_response_code(403);
    echo json_encode(array("message" => "only admin .", "error" => true));
    exit();
}

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$id                 = $_GET["id"];
$user_name          = $data->user_name;
$user_address       = $data->user_address;
$user_address_id    = $data->user_address_id;
$user_status        = $data->user_status;

$query = "UPDATE " . $table_us . "
                SET 
                    `user_name`             = :user_name,
                    `user_address`          = :user_address, 
                    `user_address_id`       = :user_address_id, 
                    `user_status`           = :user_status,
                    `user_update`           = now()
                WHERE
                    `user_code`             = :id
                ";
                
$stmt = $conn->prepare($query);
   
$stmt->bindParam(':user_name',          $user_name);
$stmt->bindParam(':user_address',       $user_address);
$stmt->bindParam(':user_address_id',    $user_address_id);
$stmt->bindParam(':user_status',        $user_status); 
$stmt->bindParam(':id',                 $id); 

$stmt->execute() or die(json_encode(
    array(
        "message"   => "execute not success edit.",
        "error"     => $stmt->errorInfo()
    ),
    http_response_code(400)
));

http_response_code(201);
echo json_encode(array("message" => "successfully.", "error" => ""));
