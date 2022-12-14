<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';

$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input")); 

if (!empty($_GET["id"])) {
    $query = "SELECT *
                 FROM " . $table_loto . "
                WHERE lotto_number = ? ";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $_GET["id"]); 

}else {
    $query = "SELECT *
    FROM " . $table_loto . "
    ";
$stmt = $conn->prepare($query);
}

try {
    if ($stmt->execute()) {

        $num = $stmt->rowCount();

        if ($num > 0) {
            http_response_code(200);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = array(
                    "lotto_no"          => $row['lotto_id'],
                    "lotto_number"      => $row['lotto_number'],
                    "lotto_lot"         => $row['lotto_lot'],
                    "lotto_book"        => $row['lotto_book'],
                    "lotto_year"        => $row['lotto_year'],
                    "lotto_date"        => $row['lotto_date'],
                    "lotto_status"      => $row['lotto_status'], 
                    "lotto_image"       => $row['lotto_image'],
                    "update_at"         => $row['update_at'],
                );
            }
        } else {
            http_response_code(404);
            $data = array("error" => "Data not found.");
        }

        $jsonlist = array(
            "data" => $data,
        );

        echo json_encode($jsonlist);
    } else {
        http_response_code(400); 
        $jsonlist = array(
            "data" => array(
                "error" => "data error connection" ,
            ),
        ); 
        echo json_encode($jsonlist);
    }
} catch (PDOException $exception) {
    http_response_code(400);
    echo json_encode(array("message" => $exception));
}
