<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    include_once "method/view.php";
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    include_once "method/edit.php";
} else {

    http_response_code(404);
    $jsonlist = array(
        "data" => array(
            "message" => "Method not right.",
            "error"   => "Method denied.",
        ),
    );
    echo json_encode($jsonlist);
}
