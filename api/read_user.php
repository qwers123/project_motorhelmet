<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once '../config/connect.php';
    include_once '../class/stat_user.php';

    $database = new Database();
    $db = $database->getConn();

    //public class from stat_user
    $user_items = new user_connect($db);

    //transfer to stmt from public function get_user
    //row count is a function of PDO
    $stmt = $user_items -> getUsers();
    $itemCnt = $stmt -> rowCount();

    if($itemCnt > 0) {
        $sysArr = array();
        $sysArr["body"] = array();
        $sysArr["itemCnt"] = $itemCnt;

        while($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $e = array(
                "id" => $id,
                "un" => $un,
                "pw" => $pw,
                "cp" => $cp
            
            );

            array_push($sysArr["body"], $e);

        }

        echo json_encode($sysArr);
    
    } else {
        http_response_code(404);
        echo json_encode (
            array("message" => "No Record Found!")
        );
    }
?>