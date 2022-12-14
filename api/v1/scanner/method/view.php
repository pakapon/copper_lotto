<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/api/v1/protected.php';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

if ($role_name != 2) {
    $query = "SELECT * FROM " . $table_loto . " WHERE user_code = '" . $world_code . "' ";
} elseif ($role_name == 2) {
    $query = "SELECT *
                    ,COUNT(lotto_book) AS booknum
                    ,(SELECT COUNT(1) FROM " . $table_loto . " ) AS all_book
                  FROM " . $table_loto . " GROUP BY lotto_book,lotto_lot ";
}

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

    if ($role_name != 2) {
        http_response_code(200);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = array(
                "lotto_book"        => $row['lotto_book'],
                "lotto_lot"         => $row['lotto_lot'],
                "lotto_set"         => $row['lotto_set'],
                "create_at"         => $row['create_at'],
                "update_at"         => $row['update_at'],
            );
        }
    } else {
        http_response_code(200);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $query_user = "SELECT  
                                    b.user_name,
                                    b.user_address,
                                    b.user_address_id,
                                    b.user_tel
                            FROM " . $table_loto . " a 
                        LEFT JOIN " . $table_us . " b ON a.user_code = b.user_code
                        WHERE lotto_book = :book 
                        GROUP BY a.user_code ";

            $stmt_user = $conn->prepare($query_user);
            $stmt_user->bindParam(':book', $row['lotto_book']);
            $stmt_user->execute();

            $user_r = null;
            while ($row_u = $stmt_user->fetch(PDO::FETCH_ASSOC)) {
                $user_r[] = array(
                    "user_name"         => $row_u['user_name'],
                    "user_address"      => $row_u['user_address'],
                    "user_address_id"   => $row_u['user_address_id'],
                    "user_tel"          => $row_u['user_tel'],
                );
            }
            $stmt_user = null;

            $data[] = array(
                "lotto_book"        => $row['lotto_book'],
                "lotto_lot"         => $row['lotto_lot'],
                "lotto_set"         => $row['lotto_set'],
                "lotto_couple"      => $row['booknum'],
                "update_at"         => $row['update_at'],
                "user_array"        => $user_r
            );
            
            $allbook    = $row['all_book'];
        }

        $query_book = " SELECT COUNT(1)AS all_setbook FROM (SELECT COUNT(lotto_book) AS all_setbook FROM " . $table_loto . " GROUP BY lotto_book ) as a; ";
        $stmt_book = $conn->prepare($query_book);
        $stmt_book->execute();
        while ($row_book = $stmt_book->fetch(PDO::FETCH_ASSOC)) {
            $allsetbook = $row_book['all_setbook'];
        }
    }
} else {
    http_response_code(404);
    $data = array("error" => "Data not found.");
}
$jsonlist = array(
    "data" => $data,
    "all_book" => $allbook,
    "all_setbook" => $allsetbook,
);

echo json_encode($jsonlist);
