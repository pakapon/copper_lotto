<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/api/v1/protected.php';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$lotto_array = $data->lotto_array; 

$query = "INSERT INTO " . $table_loto . "
            SET
                lotto_book  = :lotto_book,
                lotto_lot   = :lotto_lot,
                lotto_set   = :lotto_set,
                user_code   = :user_code,
                create_at   = now(),
                update_at   = now()
                ";
$stmt = $conn->prepare($query); 

foreach ($lotto_array as $key=>$val) {   

    $stmt->bindParam(':lotto_book',     $val->lotto_book);
    $stmt->bindParam(':lotto_lot',      $val->lotto_lot);
    $stmt->bindParam(':lotto_set',      $val->lotto_set);
    $stmt->bindParam(':user_code',      $world_code);

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
