<?php

class InvoiceSearch
{
	
    private $db_connection = null;    // database connection   
    /**
     * Checks if database connection is opened and open it if not
     */
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
                return false;
            }
        }
    }

    public function searchByInvoiceId($invoiceId){

        if ($this->databaseConnection()) {
            
            // Make sure this invoice doesn't already exist
            $query = $this->db_connection->prepare('SELECT invoices.*, users.first, users.last, projects.title FROM invoices INNER JOIN users ON invoices.userId = users.id INNER JOIN projects ON invoices.projectId = projects.id WHERE invoices.id=:id');
            $query->bindValue(':id', $invoiceId, PDO::PARAM_INT);
            $query->execute();

            header('Content-Type: application/json');
            return json_encode($query->fetch(PDO::FETCH_ASSOC));

        }
    }

    public function getServiceById($id){

        if ($this->databaseConnection()) {
            
            // Make sure this invoice doesn't already exist
            $query = $this->db_connection->prepare('
                SELECT mscServicesSelected.*, mscAnalysisServices.*, mscServiceRequest.* 
                FROM mscServicesSelected 
                INNER JOIN mscAnalysisServices 
                ON mscServicesSelected.serviceId = mscAnalysisServices.id 
                INNER JOIN mscServiceRequest
                ON mscServicesSelected.requestId = mscServiceRequest.id 
                WHERE mscServicesSelected.id=:id
            ');
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            header('Content-Type: application/json');
            return json_encode($query->fetch(PDO::FETCH_ASSOC));

        }

    }

    public function getBookingById($id){

        if ($this->databaseConnection()) {
            
            // Make sure this invoice doesn't already exist
            $query = $this->db_connection->prepare('
                SELECT instrumentBookings.*, mscInstruments.name 
                FROM instrumentBookings 
                INNER JOIN mscInstruments 
                ON instrumentBookings.instrumentId = mscInstruments.id 
                WHERE instrumentBookings.id=:id
            ');
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            header('Content-Type: application/json');
            return json_encode($query->fetch(PDO::FETCH_ASSOC));

        }

    }

    public function getTrainingById($id){

        if ($this->databaseConnection()) {
            
            // Make sure this invoice doesn't already exist
            $query = $this->db_connection->prepare('
                SELECT trainingBookings.*, mscInstruments.name 
                FROM trainingBookings 
                INNER JOIN mscInstruments 
                ON trainingBookings.instrumentId = mscInstruments.id 
                WHERE trainingBookings.id=:id
            ');
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            header('Content-Type: application/json');
            return json_encode($query->fetch(PDO::FETCH_ASSOC));

        }

    }

}

?>