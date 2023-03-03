<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/api/v1/protected.php';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));
$no_num = 0;
if (!empty($_GET["date"]) && !empty($_GET["date2"])) {
    $date_o = " o.order_create BETWEEN '" . $_GET["date"] . "' AND '" . $_GET["date2"] . "' AND ";
    $date_l = " WHERE create_at BETWEEN '" . $_GET["date"] . "' AND '" . $_GET["date2"] . "'  ";
}
if(!empty($_GET["lotto_date"])){
    $date_lotto = " WHERE lotto_date = '".$_GET["lotto_date"]."'";
}
if ($role_name != 2) {
    http_response_code(401);
    echo json_encode(array("message" => "Role Access denied.", "error" => "Authorization"));
    exit();
}
$query = "
            SELECT 
            ( SELECT COUNT(*) FROM `" . $table_loto ."` ". $date_l . " ) AS lotto ,
            ( SELECT COUNT(*) FROM `" . $table_us . "` WHERE user_type = 0 ) AS users, 
            SUM(o.order_total) AS totals
            FROM `" . $table_or . "` o  
            WHERE
                ".$date_o."
            o.payment_status = 1 ";
// echo $query;
$stmt = $conn->prepare($query); 
$stmt->execute();

$num = $stmt->rowCount();
if ($num > 0) {
    http_response_code(200); 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
        $number[] = array(
            "summary_lotto"     => (int)$row['lotto'],
            "summary_user"      => (float)$row['users'], 
            "summary_price"     => (float)$row['totals'], 
        );
    }
    $stmt=null;  
    
    $query_ta = "SELECT * FROM `" . $table_ta . "` ".$date_lotto;
    $stmt_ta = $conn->prepare($query_ta); 
    $stmt_ta->execute();
    while ($row_ta = $stmt_ta->fetch(PDO::FETCH_ASSOC)) { 
        $sall_all[] = array(
            "lotto_date"        => $row_ta['lotto_date'],
            "lotto_price"       => (float)$row_ta['total_price'], 
            "lotto_qty"         => (int)$row_ta['total_lotto'], 
        );
    }
    $stmt_ta=null;  
    
    $query_tl = "SELECT * FROM `" . $table_tl . "` ".$date_lotto;
    $stmt_tl = $conn->prepare($query_tl); 
    $stmt_tl->execute();
    while ($row_tl = $stmt_tl->fetch(PDO::FETCH_ASSOC)) { 
        $sall_loop[] = array(
            "lotto_date"        => $row_tl['lotto_date'],
            "lotto_price"       => (float)$row_tl['total_price'], 
            "lotto_qty"         => (int)$row_tl['total_lotto'], 
        );
    }
    $stmt_ta=null;  
    
    $query_tn = "SELECT * FROM `" . $table_tn . "` ".$date_lotto;
    $stmt_tn = $conn->prepare($query_tn); 
    $stmt_tn->execute();
    while ($row_tn = $stmt_tn->fetch(PDO::FETCH_ASSOC)) { 
        $sall_noloop[] = array(
            "lotto_date"        => $row_tn['lotto_date'],
            "lotto_price"       => (float)$row_tn['total_price'], 
            "lotto_qty"         => (int)$row_tn['total_lotto'], 
        );
    }
    $stmt_ta=null;  
    
    $query_tw = "SELECT * FROM `" . $table_tw . "` ".$date_lotto;
    $stmt_tw = $conn->prepare($query_tw); 
    $stmt_tw->execute();
    while ($row_tw = $stmt_tw->fetch(PDO::FETCH_ASSOC)) { 
        $sall_winner[] = array(
            "lotto_date"        => $row_tw['lotto_date'],
            "lotto_price"       => (float)$row_tw['win_price'],  
        );
    }
    $stmt_ta=null;  


    $data[] = array(
        "summary_number"        => $number,
        "summary_sale_all"      => $sall_all, 
        "summary_sale_loop"     => $sall_loop, 
        "summary_sale_noloop"   => $sall_noloop, 
        "summary_sale_winner"   => $sall_winner, 
    );

} else {
    http_response_code(404);
    $data = array();
}

$jsonlist = array( 
    "data" => ($data),
);

echo json_encode($jsonlist);
