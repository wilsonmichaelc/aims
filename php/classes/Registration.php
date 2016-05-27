<?php

/**
 * class Registration
 * handles the user registration
 *
 * @author Panique <panique@web.de>
 * @version 1.1
 */
class Registration
{
    private $db_connection            = null;    // database connection

    public  $registration_successful  = false;
    public  $verification_successful  = false;

    public  $errors                   = array(); // collection of error messages
    public  $messages                 = array(); // collection of success / neutral messages

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */
    public function __construct()
    {
      session_start();
      // if we have such a POST request, call the registerNewUser() method
      if (isset($_POST["register"])) {
          $this->registerNewUser($_POST['username'], $_POST['email'], $_POST['institution'], $_POST['newPassword'], $_POST['newPasswordRepeat'], $_POST["captcha"], $_POST['first'], $_POST['last'], $_POST['accountType']);
      }
      // if we have such a GET request, call the verifyNewUser() method
      if (isset($_GET["id"]) && isset($_GET["verificationCode"])) {
          $this->verifyNewUser($_GET["id"], $_GET["verificationCode"]);
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

    /**
     * registerNewUser()
     *
     * handles the entire registration process. checks all error possibilities, and creates a new user in the database if
     * everything is fine
     */
    private function registerNewUser($username, $email, $institution, $newPassword, $newPasswordRepeat, $captcha, $first, $last, $accountType)
    {
        // we just remove extra space on username and email
        $username  = trim($username);
        $email = trim($email);

        // check provided data validity
        if (strtolower($captcha) != strtolower($_SESSION['captcha'])) {

            $this->errors[] = "Captcha was wrong!";

        } elseif (empty($username)) {

            $this->errors[] = "Empty Username";

        } elseif (empty($institution)) {

        	$this->errors[] = "Institution must be provided!";

        } elseif (empty($newPassword) || empty($newPasswordRepeat)) {

            $this->errors[] = "Empty Password";

        } elseif ($newPassword !== $newPasswordRepeat) {

            $this->errors[] = "Password and password repeat are not the same";

        } elseif (strlen($newPassword) < 6) {

            $this->errors[] = "Password has a minimum length of 6 characters";

        } elseif (strlen($username) > 64 || strlen($username) < 2) {

            $this->errors[] = "Username cannot be shorter than 2 or longer than 64 characters";

        //} elseif (!preg_match('/^[a-z\d]{2,64}$/i', $username)) {
        } elseif (!preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $username)) {

            $this->errors[] = "Username does not fit the name scheme: only a-Z, _,  and numbers are allowed, 2 to 64 characters";

        } elseif (empty($email)) {

            $this->errors[] = "Email cannot be empty";

        } elseif (strlen($email) > 64) {

            $this->errors[] = "Email cannot be longer than 64 characters";
            $_POST['email'] = '';

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $this->errors[] = "Your email address is not in a valid email format";
            $_POST['email'] = '';

        // finally if all the above checks are ok
        } elseif (empty($first)){

        	$this->errors[] = "First Name cannot be empty";

        } elseif (empty($last)){

        	$this->errors[] = "Last Name cannot be empty";

        } elseif (empty($accountType)){

        	$this->errors[] = "Account type cannot be empty";

        } else {

            // if database connection opened
            if ($this->databaseConnection()) {

                // now it gets a little bit crazy: check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                // if so: put the value into $hash_cost_factor, if not, make $hash_cost_factor = null
                $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
                // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
                // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
                // want the parameter: as an array with, currently only used with 'cost' => XX.
                $newPassword_hash = password_hash($newPassword, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));

                // check if user already exists
                $query_check_username = $this->db_connection->prepare('SELECT username FROM users WHERE username=:username');
                $query_check_username->bindValue(':username', $username, PDO::PARAM_STR);
                $query_check_username->execute();

                $query_check_email = $this->db_connection->prepare('SELECT email FROM users WHERE email=:email');
                $query_check_email->bindValue(':email', $email, PDO::PARAM_STR);
                $query_check_email->execute();

                if ($query_check_username->fetchColumn() != false) {
                    $this->errors[] = "Sorry, that username is already taken. Please choose another one.";
                    $_POST['username'] = '';
                } else if($query_check_email->fetchColumn() != false){
                    $this->errors[] = "Sorry, that email has already been used. If you need to recover your password click <a href='passwordReset.php'>HERE</a>";
                    $_POST['email'] = '';
                } else {

                    // generate random hash for email verification (40 char string)
                    $activationHash = sha1(uniqid(mt_rand(), true));

                    // write new users data into database
                    $query_new_user_insert = $this->db_connection->prepare('INSERT INTO users (username, passwordHash, email, activationHash, registrationIp, createdAt, first, last, accountType, institution) VALUES(:username, :passwordHash, :email, :activationHash, :registrationIp, now(), :first, :last, :accountType, :institution)');
                    $query_new_user_insert->bindValue(':username', $username, PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':passwordHash', $newPassword_hash, PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':email', $email, PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':activationHash', $activationHash, PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':registrationIp', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':first', $first, PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':last', $last, PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':accountType', $accountType, PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':institution', $institution, PDO::PARAM_STR);
                    $query_new_user_insert->execute();

                    // id of new user
                    $id = $this->db_connection->lastInsertId();

                    if ($query_new_user_insert) {
                        // send a verification email
                        if ($this->sendVerificationEmail($id, $email, $activationHash)) {
                            // when mail has been send successfully
                            //$this->messages[] = "Your account has been created successfully and we have sent you an email. Please click the VERIFICATION LINK within that mail";
                            $this->registration_successful = true;
                        } else {
                            // delete this users account immediately, as we could not send a verification email
                            $query_delete_user = $this->db_connection->prepare('DELETE FROM users WHERE id=:id');
                            $query_delete_user->bindValue(':id', $id, PDO::PARAM_INT);
                            $query_delete_user->execute();
                            $this->errors[] = "Sorry, we could not send you an verification mail. Your account has NOT been created.";
                        }
                    } else {
                        $this->errors[] = "Sorry, your registration failed. Please go back and try again.";
                    }
                }
            }
        }
    }

    /*
     * sendVerificationEmail()
     * sends an email to the provided email address
     * @return boolean gives back true if mail has been sent, gives back false if no mail could been sent
     */
    public function sendVerificationEmail($id, $email, $activationHash)
    {
        $mail = new PHPMailer;

        // please look into the config/config.php for much more info on how to use this!
        // use SMTP or use mail()
        if (EMAIL_USE_SMTP) {

            // Set mailer to use SMTP
            $mail->IsSMTP();
            //useful for debugging, shows full SMTP errors
            $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            // Enable SMTP authentication
            $mail->SMTPAuth = EMAIL_SMTP_AUTH;
            // Enable encryption, usually SSL/TLS
            if (defined(EMAIL_SMTP_ENCRYPTION)) {
                $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
            }
            // Specify host server
            $mail->Host = EMAIL_SMTP_HOST;
            $mail->Username = EMAIL_SMTP_USERNAME;
            $mail->Password = EMAIL_SMTP_PASSWORD;
            $mail->Port = EMAIL_SMTP_PORT;

        } else {

            $mail->IsMail();
        }

        $mail->From = EMAIL_VERIFICATION_FROM;
        $mail->FromName = EMAIL_VERIFICATION_FROM_NAME;
        $mail->AddAddress($email);
        $mail->Subject = EMAIL_VERIFICATION_SUBJECT;

        $link = EMAIL_VERIFICATION_URL.'?id='.urlencode($id).'&verificationCode='.urlencode($activationHash);

        // the link to your register.php, please set this value in config/email_verification.php
        $mail->Body = EMAIL_VERIFICATION_CONTENT.' '.$link;

        if(!$mail->Send()) {

            $this->errors[] = "Verification Mail NOT successfully sent! Error: " . $mail->ErrorInfo;
            return false;

        } else {

            //$this->messages[] = "Verification Mail successfully sent!";
            header('Location: login.php?regcomplete');
            return true;

        }
    }

    /**
     * verifyNewUser()
     * checks the id/verification code combination and set the user's activation status to true (=1) in the database
     */
    public function verifyNewUser($id, $activationHash)
    {
        // if database connection opened
        if ($this->databaseConnection()) {
            // try to update user with specified information
            $query_update_user = $this->db_connection->prepare('UPDATE users SET userActive = 1, activationHash = NULL WHERE id = :id AND activationHash = :activationHash');
            $query_update_user->bindValue(':id', intval(trim($id)), PDO::PARAM_INT);
            $query_update_user->bindValue(':activationHash', $activationHash, PDO::PARAM_STR);
            $query_update_user->execute();

            if ($query_update_user->rowCount() > 0) {
                //$this->messages[] = "Activation was successful! You may now log in!";
                header('Location: login.php?active');
            } elseif($query_update_user->errorCode() > 0) {
                $this->errors[] = "Sorry, MySQL is reporting an error. Check your configuration.";
            } else {
                $this->errors[] = "Sorry, no such id/verification code combination here...";
            }

        }

    }

    public function getSideBarMessage(){
        $entryId=1;
        if ($this->databaseConnection()) {
            // database query, getting all the info of the selected user
            $query = $this->db_connection->prepare('SELECT html FROM sideBarMessage WHERE id=:id');
            $query->bindValue(':id', $entryId, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchColumn();
        }
    }

}
?>
