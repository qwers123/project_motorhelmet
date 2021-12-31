<?php   
    class user_connect {
        private $conn;
        private $db_table = "user";

        public $id;
        public $un;
        public $pw;
        public $cp;

        public function __construct ($db) {
            $this->conn = $db;
        }
    

        public function getUsers() {
            try {
                $sqlQuery = "SELECT id, un, pw, cp FROM ". $this->db_table ." ORDER BY id";
                
                $stmt = $this -> conn -> prepare($sqlQuery);
                if($stmt -> execute()) {
                    return $stmt;

                }
                
                return false;
   
            } catch (PDOException $ex) {
                echo "Error in Retrieving Value " . $ex->getMessage();
            }

        }

        public function getUser($db_un) {
            try {
                $sqlQuery = "SELECT * FROM ". $this->db_table ." WHERE un = '". $db_un . "'"; 
                
                $stmt = $this -> conn -> prepare($sqlQuery);
                if($stmt -> execute()) {
                    return $stmt;

                }

                return false;
                

            } catch (PDOException $ex) {
                echo "Error in Retrieving Value " . $ex->getMessage();

            }
        }
    
    }

?>