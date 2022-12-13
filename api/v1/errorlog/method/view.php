<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config/globalfuction.php';
include 'fuction.php';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$jsonin = file_get_contents("php://input");
$data = json_decode($jsonin);

$query = "SELECT * FROM `scanner_log` ORDER BY `create` DESC;";
$stmt = $conn->prepare($query);
$stmt->execute()or die(json_encode(
    array(
        "message"   => "execute not success.",
        "error"     => $stmt->errorInfo()
    ),
    http_response_code(400)
));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = array(

        "log"   => json_decode($row['log']),
        "time"  => $row['create'],

    );
}
$jsonlist = array(
    "data" => $data,
);

echo json_encode($jsonlist);
http_response_code(200);