<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // The request is using the POST method
    // include_once "method/view.php"; 
    http_response_code(404);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The request is using the POST method
    include_once "method/login.php";

} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // The request is using the POST method
    include_once "method/refresh.php"; 

} elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    // The request is using the POST method
    // include_once "method/edit.php";  
    http_response_code(404);

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // The request is using the POST method
    http_response_code(404);
 
} else { 
    http_response_code(404);
    $jsonlist = array(
        "payload" => array( 
            "message" => "Method not right.",
            "error"   => "Method denied.",
        ),
    );
    echo json_encode($jsonlist);
}