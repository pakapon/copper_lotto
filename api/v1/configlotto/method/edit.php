<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config/globalfuction.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/api/v1/protected.php';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$start_order        = $data->start_order;
$cutoff_order       = $data->cutoff_order; 
$date_lotto         = $data->date_lotto; 

if ($role_name != 2) {
    http_response_code(401);
    echo json_encode(array("message" => "Role Access denied.", "error" => "Authorization"));
    exit();
} 

$query = "UPDATE `" . $table_cf . "`  
                SET 
                    `start_order`       = :start,
                    `cutoff_order`      = :cutoff,
                    `date_lotto`        = :date_lotto
                WHERE 
                    no = 0
                    ";
$stmt = $conn->prepare($query);

$stmt->bindParam(':start', $start_order);
$stmt->bindParam(':cutoff', $cutoff_order);
$stmt->bindParam(':date_lotto', $date_lotto);
$stmt->execute() or die(json_encode(
    array(
        "message"   => "execute not success.",
        "error"     => $stmt->errorInfo()
    ),
    http_response_code(400)
));

http_response_code(201);
echo json_encode(array("message" => "successfully.", "error" => ""));
