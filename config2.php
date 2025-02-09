<?php 
    
    class Database {
        private $host = "localhost";
        private $username = "root";
        private $pass = "";
        private $dbname = "e-commerce";

        public function getConnection() {

            try {

                $conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->pass);
                
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return $conn;

            } catch (PDOException $e) {
                echo "ERROR connecting to the database: ". $e->getMessage();
            }

        }
    }
?>