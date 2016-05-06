<?php
class SuperUser
{

    private $db_connection            		= null;    // database connection

    public  $errors                   		= array(); // collection of error messages
    public  $messages                 		= array(); // collection of success / neutral messages

    public function __construct()
    {
  		if(isset($_POST["updateSuperAdmin"]) && $_SESSION['isSuperUser']){
  			//$this->updateSuperAdmin($_POST['id'], $_POST['newSuperStatus'], $_POST['newAdminStatus']);
        $this->updateSuperAdmin($_POST['id'], $_POST['newAdminStatus']);
  		}
    }

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
                $this->errors[] = "Database connection problem.";
                return false;
            }
        }
    }

    public function getUsers(){
      if ($this->databaseConnection()) {
        $query = $this->db_connection->prepare('SELECT id, first, last, email, accountType, isAdmin, isSuperUser, userActive  FROM users');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
      }
    }

    //public function updateSuperAdmin($id, $newSuperStatus, $newAdminStatus){
    private function updateSuperAdmin($id, $newAdminStatus){
      if ($this->databaseConnection()) {
        //$query = $this->db_connection->prepare('UPDATE users SET isSuperUser=:newSuperStatus, isAdmin=:newAdminStatus WHERE id=:id');
        $query = $this->db_connection->prepare('UPDATE users SET isAdmin=:newAdminStatus WHERE id=:id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        //$query->bindValue(':newSuperStatus', $newSuperStatus, PDO::PARAM_INT);
        $query->bindValue(':newAdminStatus', $newAdminStatus, PDO::PARAM_INT);
        if($query->execute()){
          $this->messages[] = "Success!";
        }else{
          $this->errors[] = "Failed to update.";
        }
      }
    }

    public function getBookingLogs($limit=50){
      if ($this->databaseConnection() && $_SESSION['isAdmin']) {
        $query = $this->db_connection->prepare('SELECT * FROM bookingLog ORDER BY modifiedAt DESC LIMIT :limit');
        $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        if($query->execute()){
          return $query->fetchAll(PDO::FETCH_ASSOC);
        }else{
          $this->errors[] = "Failed to get booking logs.";
        }
      }
    }

}
