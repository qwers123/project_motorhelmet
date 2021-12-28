<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once '../config/connect.php';
    include_once '../class/stat_user.php';

    $database = new Database();
    $db = $database->getConn();

    //public class from stat_user
    $user_item = new user_connect($db);
    
    $db_un = "user";
    $db_pw = "user";

    //transfer to stmt from public function get_user
    //row count is a function of PDO
    $stmt = $user_item -> getUser($db_un, $db_pw);

    if ($stmt) {
        echo "login";
        $login = $stmt -> fetch(PDO::FETCH_ASSOC);
    } else {
        echo "failed to login";
    }

?>