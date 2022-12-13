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

if ($_GET["code"] != '') {
    $id = " AND user_code = '" . $_GET["code"] . "' ";
}
// if ($_GET["code"] != '') {
//     $id = " user_code = '" . $_GET["id"] . "'";
// }

$query = "SELECT * FROM " . $table_us . " a  WHERE  1=1 " . $id;

$stmt = $conn->prepare($query);
$stmt->execute() or die(json_encode(
    array(
        "message"   => "execute not success.",
        "error"     => $stmt->errorInfo()
    ),
    http_response_code(400)
));
$num = $stmt->rowCount();

if ($num > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['user_lineid'] != null or $row['user_lineid'] != '') {
            $line = "connect";
        } else {
            $line = "not connect";
        }
        $data[] = array(

            "user_code"         => $row['user_code'],
            "user_tel"          => $row['user_tel'],
            "user_type"         => $row['user_type'],
            "user_name"         => $row['user_name'],
            "user_address"      => $row['user_address'],
            "user_address_id"   => $row['user_address_id'],
            "user_status"       => $row['user_status'],
        );
    }
} else {
    http_response_code(404);
    $data = array("error" => "Data not found.");
}

$jsonlist = array(
    "data" => $data,
);

http_response_code(200);
echo json_encode($jsonlist);
