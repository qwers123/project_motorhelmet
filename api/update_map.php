<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../config/connect.php';
    include_once '../class/stat_map.php';

    $database = new Database();
    $db = $database->getConn();

    $map_item = new map_connect($db);

    $map_data = json_decode(file_get_contents("php://input"));

    $map_item->un = $map_data->un;
    $map_item->lat = $map_data->lat;
    $map_item->lng = $map_data->lng;
    $map_item->move = $map_data->move;
    $map_item->motor = $map_data->motor;

    if($map_item->setMap()) {
        echo 'Map Updated';
    } else {
        echo 'Error in Updating';
    }
?>