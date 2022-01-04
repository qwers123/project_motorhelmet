<?php

session_start();
 
    // Check if the user is logged in, if not then redirect him to login page
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: index.php");
        exit;
    }
    $map_err = "";

    $un = $_SESSION["username"];
    $cp = $_SESSION["cp"];

    // Include config file
    include_once 'config/connect.php';
    include_once 'class/stat_map.php';

    
    
    $database = new Database();
    $db = $database->getConn();

    $items = new map_connect($db);

    $stmt_mapData = $items->getMaps($un);
    $stmt_mapLatest = $items->getMap_Latest($un);

    $move = $motor = $dnt = $lat = $lng = "";
    $dnt_arr = array();
    //
    //
    $page = $_SERVER['PHP_SELF'];
    $sec = "30";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">

        <title>Motorcycle Tracker</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link href="css/styles.css" rel="stylesheet" />
        <!-- JQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        
    </head>
    <body>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#">Hello <?php echo htmlspecialchars($un); ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">More</a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#">How to Use</a></li>
                                <li><hr class="dropdown-divider" /></li>
                                <li><a class="dropdown-item" href="#">Contact</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Page content-->
        <div class="container">
            <div class="text-center mt-5">
                <h1>Motorcycle Location</h1>
                <p class="lead">Check the location of your Motorcycle</p>
            </div>
        </div>
        <div class="container">
            
            <table class="table" style="margin: 0 auto; al" >
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" >Motorcycle Status</th>
                        <th scope="col" >Moving Status</th>
                        <th scope="col" >Date and Time</th>
                    </tr>

                </thead>
                <?php 
                        $cnt_stmt_mapData = $stmt_mapData->rowCount();
                        $cnt_stmt_mapLatest = $stmt_mapLatest->rowCount();

                        if(($cnt_stmt_mapData==0) or ($cnt_stmt_mapLatest==0)) {
                            $map_err = "No data Found!";
                            $motor = " No data ";
                            $move = " No data ";
                            $dnt = "No data";

                        } else {
                            $rows_Latest = $stmt_mapLatest->fetchAll();
                            foreach($rows_Latest as $row_Latest) {
                                $motor = $row_Latest['motor'];
                                $move = $row_Latest['move'];
                                $dnt = $row_Latest['dnt'];
                                $lat = $row_Latest['lat'];
                                $lng = $row_Latest['lng'];

                            }
                        }

                        $curl= curl_init();
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_URL, 'http://motors.outcastph.com/api/read_latest_map.php');
                        $res = curl_exec($curl);
                        
                        $jo = json_decode($res, TRUE);
                        //var_dump($jo);
                        curl_close($curl);

                        /*
                        
                        $latest_json = file_get_contents('php://input');
                        $json_decoded = json_decode($latest_json);
                        
                        */
                        
                        //echo "\n try \n";

                        
                        $dnt = $jo["body"][0]["dnt"];
                        $motor = $jo["body"][0]["motor"];
                        $move = $jo["body"][0]["move"];

                        date_default_timezone_set('Asia/Manila');
                        $date = new DateTime("now");
                        $new_dnt = New DateTime($dnt);
                        /*
                        echo "new time: ";
                        echo $date->format('G:ia');
                        echo "\n";
                        echo "time from dnt: ";
                        echo $new_dnt->format('G:ia');
                        echo "\n";
                        */
                        $time_diff = $date->diff($new_dnt);
                        $mins = $time_diff->days * 24 * 60;
                        $mins += $time_diff->h * 60;
                        $mins += $time_diff-> i;

                        if ($mins > 5) {
                            $motor = "DISCONNECTED";
                            $move = "DISCONNECTED";
                        }
                        
                        

                        //echo $mins . " Minutes.";
                        
                        switch (json_last_error()) {
                            case JSON_ERROR_NONE:

                            break;
                            case JSON_ERROR_DEPTH:
                                echo ' - Maximum stack depth exceeded';
                            break;
                            case JSON_ERROR_STATE_MISMATCH:
                                echo ' - Underflow or the modes mismatch';
                            break;
                            case JSON_ERROR_CTRL_CHAR:
                                echo ' - Unexpected control character found';
                            break;
                            case JSON_ERROR_SYNTAX:
                                echo ' - Syntax error, malformed JSON';
                            break;
                            case JSON_ERROR_UTF8:
                                echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                            break;
                            default:
                                echo ' - Unknown error';
                            break;
                        }
                        
                        echo PHP_EOL; 
                        
                    ?>

                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($motor) ?></td>
                        <td><?php echo htmlspecialchars($move) ?></td>
                        <td> 
                            <select class="form-select" id="dnt-select" aria-label="Default select example">
                                <option selected><?php echo date("M. d, Y, h:iA",strtotime($dnt))  ?></option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row">
            <?php 
                if(!empty($map_err)){
                    echo '<div class="alert alert-warning alert-dismissible fade show align-middle" role="alert">' . $map_err . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button></div>';
                }        
            ?>
            <div class="col"><hr></div>
        </div>

        <!-- div to show the google map -->
        <div id="googleMap" style="width:90%;height:600px;margin: auto;"></div>
        <!-- end -->

        <div id="dom-lat" style="display: none">
            <?php
                $out1 = $jo["body"][0]["lat"];
                $out2 = floatval($out1);
                echo $out2;
            ?>
        </div>
        <div id="dom-lng" style="display: none">
            <?php
                $out1 = $jo["body"][0]["lng"];
                $out2 = floatval($out1);
                echo $out2;
            ?>
        </div>
        <div id="dom-move" style="display: none">
            <?php
                echo $move;
            ?> 
        </div>
        <div id="dom-motor" style="display: none">
            <?php
                echo $motor;
            ?> 
        </div>
        <div id="dom-total-time" style="display: none">
            <?php
                echo $mins;
            ?> 
        </div>
        

        <script>
            
            
            var url = 'http://motors.outcastph.com/api/read_latest_map.php'
            var var_lat = document.getElementById("dom-lat");
            var val_lat = parseFloat(var_lat.textContent);
            var var_lng = document.getElementById("dom-lng");
            var val_lng = parseFloat(var_lng.textContent);
            var var_motor = document.getElementById("dom-motor").textContent;
            var val_motor = var_motor.replace(/\s/g, '');
            var var_move = document.getElementById("dom-move").textContent;
            var val_move = var_move.replace(/\s/g, '');
            var var_total_time = document.getElementById("dom-total-time");
            var val_total_time = parseFloat(var_total_time.textContent);

            console.log("lat");
            console.log(val_lat);
            console.log("lng");
            console.log(val_lng);
            console.log("motor");
            console.log(val_motor);
            console.log("move");
            console.log(val_move);
            console.log("total time");
            console.log(val_total_time);
            
            
            /*
            
            async function get_latlng() {
                let url = 'http://motors.outcastph.com/api/read_latest_map.php'
                let obj = await  (await fetch(url)).json();

                return obj;
            }
            
            var tags;
            var obj_p;

            (async () => {
                tags = await get_latlng()

                //document.getElementById("lat-lng").innerHTML = JSON.stringify(tags);

                try {
                    obj_p = JSON.parse(tags);
                } catch (e) {
                    console.log(e);
                    obj_p = tags;
                }
            

                val_lat = tags["body"]["id"];
                val_lng = tags["body"]["lng"];

                console.log("lat");
                console.log(val_lat);
                console.log("lng");
                console.log(val_lng);

            })()
            function printVal(tags) {
                for (var k in tags) {
                    if(tags[k] instanceof Object) {
                        printVal(tags[k]);
                    } else {
                        document.write(tags[k] + "<br");
                    };
                }
            };
            document.getElementById("lat-lng").innerHTML = printVal(obj_p);
            */

            if (val_lat=="") {
                val_lat = 13.771041628364877;
                val_lng = 121.0646232998532;
                
            }

            function myMap() {
                var coordinates = {
                    lat: val_lat,
                    lng: val_lng
                };
                
                var mapProp= new google.maps.Map(document.getElementById("googleMap"), {
                    zoom: 20,
                    center: coordinates,
                });

               
                
                if(val_motor == "ON" && val_move == "ON") {

                    var marker_moveOn_motorOn = new google.maps.Marker({
                        position: coordinates,
                        map: mapProp,
                        icon: {
                            url: "http://maps.google.com/mapfiles/ms/micons/motorcycling.png",
                            labelOrigin: new google.maps.Point(75, 32),
                            size: new google.maps.Size(32, 32),
                            anchor: new google.maps.Point(16, 32)
                        }
                    });
                    
                } else if(val_motor == "OFF" && val_move == "ON") {
                    var marker_moveOn_motorOff = new google.maps.Marker({
                        position: coordinates,
                        map: mapProp,
                        icon: {
                            url: "http://maps.google.com/mapfiles/kml/paddle/stop-lv.png",
                            labelOrigin: new google.maps.Point(75, 32),
                            size: new google.maps.Size(32, 32),
                            anchor: new google.maps.Point(16, 32)
                        }
                    });
                } else {
                    var marker_not_connected = new google.maps.Marker({
                            position: coordinates,
                            map: mapProp,
                            icon: {
                                url: "http://maps.google.com/mapfiles/kml/pal3/icon34.png",
                                labelOrigin: new google.maps.Point(75, 32),
                                size: new google.maps.Size(32, 32),
                                anchor: new google.maps.Point(16, 32)
                            },
                            label: {
                                text: "DISCONNECTED",
                                color: "#C70E20",
                                fontWeight: "bold"
                            }
                        });
                }
                
            }
            </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAwQBLkpYv8jhjtshFbSBHpW_jznoMPgxQ&callback=myMap"></script>
            

        <!-- Footer -->
        <footer class="page-footer font-small special-color-dark pt-4">

            <!-- Copyright -->
            <div class="footer-copyright text-center py-3">For Development Purposes: 
            <a href="https://outcastph.com">OUTCASTPH</a>
            </div>
            <!-- Copyright -->

        </footer>
        <!-- Footer -->


        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>

    
</html>
