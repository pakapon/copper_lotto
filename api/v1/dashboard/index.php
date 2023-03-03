<?php 

if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    include_once "method/view.php";

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // include_once "method/create.php";
    http_response_code(404);
    $jsonlist = array(
        "data" => array( 
            "message" => "Method not right.",
            "error"   => "Method denied.",
        ),
    );
    echo json_encode($jsonlist); 
    exit();

} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // include_once "method/edit.php";
    http_response_code(404);
    $jsonlist = array(
        "data" => array( 
            "message" => "Method not right.",
            "error"   => "Method denied.",
        ),
    );
    echo json_encode($jsonlist); 
    exit();

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
   
    // include_once "method/delete.php";

    http_response_code(404);
    $jsonlist = array(
        "data" => array( 
            "message" => "Method not right.",
            "error"   => "Method denied.",
        ),
    );
    echo json_encode($jsonlist); 
    exit();
 
} else { 
    http_response_code(404);
    $jsonlist = array(
        "data" => array( 
            "message" => "Method not right.",
            "error"   => "Method denied.",
        ),
    );
    echo json_encode($jsonlist); 
    exit();
}
