<?php

include $_SERVER['DOCUMENT_ROOT'] . '/config/database.php'; 

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$query = "SELECT *  FROM `" . $table_cf . "` ;";

$stmt = $conn->prepare($query);
$stmt->execute() or die(json_encode(
    array(
        "message"   => "execute not success.",
        "error"     => $stmt->errorInfo(),
    ),
    http_response_code(400)
)); 

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $data = array(
        "start_order_time"  => $row['start_order'],
        "cutoff_order_time" => $row['cutoff_order'],
        "date_lotto"        => $row['date_lotto'],
    );
}

$jsonlist = array(
    "data" => $data,
);

http_response_code(200);
echo json_encode($jsonlist);
?>