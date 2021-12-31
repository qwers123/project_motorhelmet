<?php
    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json; charset=utf-8');
/*
    session_start();
    $un = $_SESSION["username"];

*/
    include_once '../config/connect.php';
    include_once '../class/stat_map.php';


    //test
    $un = 'user';
    //end
    $database = new Database();
    $db = $database->getConn();

    $map_item = new map_connect($db);
    
    $stmt = $map_item->getMap_Latest($un);
    $itemCount = $stmt->rowCount();

    if($itemCount > 0) {
        $sysArr = array();
        $sysArr["body"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $e = array(
                "id" => $id,
                "un" => $un,
                "lat" => $lat,
                "lng" => $lng,
                "move" => $move,
                "motor" => $motor,
                "dnt" => $dnt
            );
            array_push($sysArr["body"], $e);
        }

        $json_encoded = safe_json_encode(utf8ize($sysArr));
        echo $json_encoded;

    } else {
        http_response_code(404);
        echo json_encode (
            array("message" => "No Record Found!")
        );
    }


    function safe_json_encode($value, $options = 0, $depth = 512, $utfErrorFlag = false) {
        
        return json_encode($value, $options, $depth);
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $encoded;
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_UTF8:
                $clean = utf8ize($value);
                if ($utfErrorFlag) {
                    return 'UTF8 encoding error'; // or trigger_error() or throw new Exception()
                }
                return safe_json_encode($clean, $options, $depth, true);
            default:
                return 'Unknown error'; // or trigger_error() or throw new Exception()
    
        } 
        
    }
    
    function utf8ize($mixed) {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = utf8ize($value);
            }
        } else if (is_string ($mixed)) {
            return utf8_encode($mixed);
        }
        return $mixed;
    }
?>
