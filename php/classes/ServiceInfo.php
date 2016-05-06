<?php

class ServiceInfo
{
    private $db_connection = null; 


    public function __construct()
    {

    }

    private function databaseConnection()
    {
        // connection already opened
        if ($this->db_connection != null) {
            return true;
        } else {
            // create a database connection, using the constants from config/config.php
            try {
                $this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME, DB_USER, DB_PASS);
                return true;

            // If an error is catched, database connection failed
            } catch (PDOException $e) {
                $this->errors[] = "Database connection problem.";
                return false;
            }
        }
    }

    public function getAnalysisServices(){
    
        if ($this->databaseConnection()) {
            
            $query = $this->db_connection->prepare('SELECT * FROM mscAnalysisServices');
            $query->execute();
            header('Content-Type: application/json');
            return json_encode($query->fetch(PDO::FETCH_ASSOC));
                        
        }
        
    }
}

?>