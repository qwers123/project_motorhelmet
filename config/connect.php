<?php

    class Database {
        private $host           = "piggery.outcastph.com";
        private $database_name  = "outcastp_piggery";
        private $username       = "outcastp_apdb";
        private $password       = "MjXmA.z9XB.3";

        public $conn;

        public function getConn() {
            $this->conn = null;
            try {
                $this->conn = new PDO("mysql:host=". $this->host . ";dbname=" . $this->database_name, $this->username, $this->password);
                $this->conn -> exec("set names utf8");
                
            } catch (PDOException $exception) {
                echo "Database could not be connected: " . $exception->getMessage();
            }

            return $this->conn;
        }
    }

?>