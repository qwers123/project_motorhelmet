<?php   
    class map_connect {
        private $conn;
        private $db_table = "stat";

        public $id;
        public $un;
        public $lat;
        public $lng;
        public $move;
        public $motor;
        public $dnt;


        public function __construct ($db) {
            $this->conn = $db;
        }

        public function getMaps($db_un) {

            try {
                $sqlQuery = "SELECT * FROM ". $this->db_table ." WHERE un = '". $db_un . "' ORDER BY dnt DESC"; 
                
                $stmt = $this -> conn -> prepare($sqlQuery);
                if($stmt -> execute()) {
                    return $stmt;

                }

                return false;
                

            } catch (PDOException $ex) {
                echo "Error in Retrieving Value " . $ex->getMessage();

            }
        }

        public function getMap_Latest($db_un) {

            try {
                $sqlQuery = "SELECT * FROM ". $this->db_table ." WHERE un = '". $db_un . "' ORDER BY dnt DESC LIMIT 1"; 
                
                $stmt = $this -> conn -> prepare($sqlQuery);
                if($stmt -> execute()) {
                    return $stmt;

                }

                return false;
                

            } catch (PDOException $ex) {
                echo "Error in Retrieving Value " . $ex->getMessage();

            }

        } 


        public function setMap() {
            try {

                $sqlQuery = "INSERT INTO ". $this->db_table . "
                    SET
                    un = :un,
                    lat = :lat,
                    lng = :lng,
                    move = :move,
                    motor = :motor
                ";
                $stmt = $this->conn->prepare($sqlQuery);

                $this->un=htmlspecialchars(strip_tags($this->un));
                $this->lat=htmlspecialchars(strip_tags($this->lat));
                $this->lng=htmlspecialchars(strip_tags($this->lng));
                $this->move=htmlspecialchars(strip_tags($this->move));
                $this->motor=htmlspecialchars(strip_tags($this->motor));

                $stmt->bindParam(":un", $this->un);
                $stmt->bindParam(":lat", $this->lat);
                $stmt->bindParam(":lng", $this->lng);
                $stmt->bindParam(":move", $this->move);
                $stmt->bindParam(":motor", $this->motor);

                if($stmt->execute()) {
                    return true;
                }
                return false;
                
            } catch (PDOException $ex) {
                echo "Error in Sending Value " . $ex->getMessage();
            }
        }

    }

?>
