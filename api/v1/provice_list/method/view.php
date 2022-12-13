<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
// include_once $_SERVER['DOCUMENT_ROOT'] . '/api/v1/protected.php';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

if ($_GET["page"] > 0) {
    $page = " LIMIT 20 OFFSET " . (($_GET["page"] - 1) * 20) . " ";
    $no_num = (($_GET["page"] - 1) * 20);
}
if ($_GET["id"] != '') {
    $id = " AND `p_id` = '" . $_GET["id"] . "'";
}
if ($_GET["postcode"] != '') {
    $pt = " AND `postcode` = '" . $_GET["postcode"] . "'";
}
if ($_GET["province"] != '') {
    $po = " AND `province` LIKE '%" . $_GET["province"] . "%'";
}
if ($_GET["aumper"] != '') {
    $am = " AND `aumper` LIKE '%" . $_GET["aumper"] . "%'";
}
if ($_GET["tumbon"] != '') {
    $tm = " AND `tumbon` LIKE '%" . $_GET["tumbon"] . "%'";
}
$query = "SELECT * 
            ,CEILING( (SELECT COUNT(*) FROM `" . $table_add . "`  WHERE 1=1 " . $pt . $po . $am . $tm . $id . ") / 20) AS maxpage
            FROM `" . $table_add . "`  WHERE 1=1 " . $pt . $po . $am . $tm . $id  . $page;

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
    $i = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $maxpage = $row['maxpage'];
        $data[] = array(
            "no"                => $i + $no_num,
            "address_id"        => $row['p_id'],
            "postcode"          => $row['postcode'],
            "tumbon"            => $row['tumbon'],
            "aumper"            => $row['aumper'],
            "province"          => $row['province'],
            "sector"            => $row['sector'],
            "sector_group"      => $row['sector_group']
        );
        $i++;
        http_response_code(200);
    }
} else {
    http_response_code(404);
    $data = array("error" => "Data not found.");
}

$jsonlist = array(
    "status" => 200,
    "data" => $data,
);

echo json_encode($jsonlist);
