<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/api/v1/protected.php';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$user_password      = $data->user_password;

if ($role_name == 1) {
    $query = "UPDATE " . $table_us . "
                SET   
                    `user_password`       = :user_password,
                    `user_update`         = now()
                WHERE
                    `user_code`           = :user_code
                ";

    $stmt = $conn->prepare($query);

    $password_hash = password_hash($user_password, PASSWORD_BCRYPT);
    $stmt->bindParam(':user_password',  $password_hash);
    $stmt->bindParam(':user_code',      $world_code);

    $stmt->execute() or die(json_encode(
        array(
            "message"   => "execute not success.",
            "error"     => $stmt->errorInfo()
        ),
        http_response_code(400)
    ));
} elseif ($role_name == 2) {

    $query = "UPDATE " . $table_us . "
    SET   
        `user_password`       = :user_password,
        `user_update`         = now()
    WHERE
        `user_code`           = :user_code
    ";

    $stmt = $conn->prepare($query);

    $password_hash = password_hash($user_password, PASSWORD_BCRYPT);
    $stmt->bindParam(':user_password',  $password_hash);
    $stmt->bindParam(':user_code',      $_GET["user_code"]);

    $stmt->execute() or die(json_encode(
        array(
            "message"   => "execute not success.",
            "error"     => $stmt->errorInfo()
        ),
        http_response_code(400)
    ));
}else{
    http_response_code(404);
    $data = array("error" => "Data not found.");
}

http_response_code(201);
echo json_encode(array("message" => "successfully.", "error" => ""));
