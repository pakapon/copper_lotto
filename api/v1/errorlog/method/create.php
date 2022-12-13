<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config/globalfuction.php';
include 'fuction.php';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$jsonin = file_get_contents("php://input");
$data = json_decode($jsonin);

$query = "INSERT INTO `" . $table_slog . "`
                SET `log`    = :data,
                    `create`  =now()
                    ;";

$stmt = $conn->prepare($query);
$stmt->bindParam(':data', $jsonin);
$stmt->execute()or die(json_encode(
    array(
        "message"   => "execute not success.",
        "error"     => $stmt->errorInfo()
    ),
    http_response_code(400)
));

http_response_code(200);
echo json_encode(array("message" => "successfully."));
