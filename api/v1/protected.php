<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use \Firebase\JWT\JWT;

$jwt = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));
$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
$jwt = $arr[1];

if (empty($jwt)) {
    http_response_code(200);
    echo json_encode(array(
        "message" => "somthing wrong please try agin! ",
        "error" => "token null",
    ));
    exit();
}

if (!empty($jwt)) {
    try {

        $p_query = "  SELECT * 
                    FROM 
                        `" . $table_aut . "` a
                    JOIN
                        `" . $table_us . "` b ON a.user_code = b.user_code
                    LEFT JOIN `".$table_po."` c on a.user_code = c.point_usercode
                    WHERE token = ? ";
                    
        $p_stmt = $conn->prepare($p_query);
        $p_stmt->bindParam(1, $jwt);
        $p_stmt->execute();
        $num = $p_stmt->rowCount();
        $row = $p_stmt->fetch(PDO::FETCH_ASSOC);
        $role_name      = $row["user_type"];
        $world_email    = $row["user_email"];
        $world_code     = $row["user_code"];
        $world_phone    = $row["user_tel"];
        $world_scanner  = $row["scanner"];
        $world_line     = $row["user_lineid"];
        $world_point    = $row['POINT'];

        if ($num === 0) {
            http_response_code(401);
            echo json_encode(array(
                "message" => "token not valid please try agin! ",
                "error" => "",
            ));
            exit();
        }
    } catch (Exception $q) {
        http_response_code(401);
        echo json_encode(array(
            "message" => "somthing wrong please try agin! ",
            "error" => $q,
        ));
        exit();
    }

    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        $access = true;
    } catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
        $access = false;
        exit();
    }
} else {
    http_response_code(401);

    echo json_encode(array(
        "message" => "Access denied.",
        "error" => "Authorization not access"
    ));
    $access = false;
    exit();
}
