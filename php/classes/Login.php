<?php

/**
 * class Login
 * handles the user login/logout/session
 *
 * @author Panique <panique@web.de>
 */
class Login
{
    /** @var object $db_connection The database connection */
    private $db_connection = null;
    /** @var int $hash_cost_factor The (optional) cost factor for the hash calculation */
    private $hash_cost_factor = null;

    /** @var int $id The user's id */
    private $id = null;
    /** @var string $username The user's name */
    private $username = "";
    /** @var string $email The user's mail */
    private $email = "";
    /** @var string $passwordHash The user's hashed and salted password */
    private $passwordHash = "";
    /** @var boolean $isLoggedIn The user's login status */
    private $isLoggedIn = false;
    /** @var string $passwordResetHash The user's password reset hash */
    private $passwordResetHash = "";
    /** @var string $user_gravatar_image_url The user's gravatar profile pic url (or a default one) */
    public $user_gravatar_image_url = "";
    /** @var string $user_gravatar_image_tag The user's gravatar profile pic url with <img ... /> around */
    public $user_gravatar_image_tag = "";

    /** @var boolean $password_reset_link_is_valid Marker for view handling */
    private $password_reset_link_is_valid  = false;
    /** @var boolean $passwordResetSuccessful Marker for view handling */
    private $passwordResetSuccessful = false;

    /** @var array $errors Collection of error messages */
    public $errors = array();
    /** @var array $messages Collection of success / neutral messages */
    public $messages = array();

    private $isAdmin = false;

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */
    public function __construct()
    {
        // create/read session
        session_start();

        // check the possible login actions:
        // 1. logout (happen when user clicks logout button)
        // 2. login via session data (happens each time user opens a page on your php project AFTER he has successfully logged in via the login form)
        // 3. login via cookie
        // 4. login via post data, which means simply logging in via the login form. after the user has submit his login/password successfully, his
        //    logged-in-status is written into his session data on the server. this is the typical behaviour of common login scripts.

        // if user tried to log out
        if (isset($_GET["logout"])) {

            $this->doLogout();

        // if user has an active session on the server
        } elseif (!empty($_SESSION['username']) && ($_SESSION['isLoggedIn'] == 1)) {

            $this->loginWithSessionData();

            // checking for form submit from editing screen
            if (isset($_POST["user_edit_submit_name"])) {

                $this->editUsername();

            } elseif (isset($_POST["user_edit_submit_email"])) {

                $this->editUserEmail();

            } elseif (isset($_POST["editPassword"])) {

                $this->editUserPassword();

            }

        // login with cookie
        } elseif (isset($_COOKIE['rememberme'])) {

            $this->loginWithCookieData();

        // if user just submitted a login form
        } elseif (isset($_POST["login"])) {

            $this->loginWithPostData();

        }

        // checking if user requested a password reset mail
        if (isset($_POST["requestPasswordReset"])) {

            $this->setPasswordResetDatabaseTokenAndSendMail(); // maybe a little bit cheesy

        } elseif (isset($_GET["u"]) && isset($_GET["v"])) {

            $this->checkIfEmailVerificationCodeIsValid();

        } elseif (isset($_POST["submitNewPassword"])) {

            $this->editNewPassword();

        }

        // checking if the user read and agreed to the eula
        if (isset($_POST['eula'])){
	        $this->updateEula();
        }

        if (isset($_GET['sessionexpired'])){
	        $this->messages[] = 'Session Expired!';
        }

        if (isset($_GET['regcomplete'])){
	        $this->messages[] = "Your account has been created successfully and we have sent you an email. Please click the VERIFICATION LINK within that mail!";
        }

        if (isset($_GET['active'])){
	        $this->messages[] = "Activation was successful! You may now log in!";
        }

        if (isset($_GET['verify'])){
	        $this->messages[] = "Please check your email and click on the reset link we sent you!";
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
                $this->errors[] = "Database connection problem." . $e->getMessage();
                return false;
            }
        }
    }

    /**
     * Search into database for the user data of username specified as parameter
     * @return user data as an object if existing user
     * @return false if username is not found in the database
     */
    private function getUserData($username)
    {
        // if database connection opened
        if ($this->databaseConnection()) {

            // database query, getting all the info of the selected user
            $query_user = $this->db_connection->prepare('SELECT * FROM users WHERE username = :username limit 1');
            $query_user->bindValue(':username', $username, PDO::PARAM_STR);
            $query_user->execute();
            //print_r($query_user->fetch(PDO::FETCH_ASSOC));
            // get result row (as an object)
            //return $query_user->fetchObject();
            return $query_user->fetch(PDO::FETCH_OBJ);

		} else {

            return false;
        }
    }

    private function loginWithSessionData()
    {
    	$this->id = $_SESSION['id'];
        $this->username = $_SESSION['username'];
        $this->email = $_SESSION['email'];
        $this->first = $_SESSION['first'];
        $this->last = $_SESSION['last'];
        $this->accountType = $_SESSION['accountType'];
        $this->readEULA = $_SESSION['readEULA'];
		    $this->isAdmin = $_SESSION['isAdmin'];
        $this->isSuperUser = $_SESSION['isSuperUser'];

        // set logged in status to true, because we just checked for this:
        // !empty($_SESSION['username']) && ($_SESSION['isLoggedIn'] == 1)
        // when we called this method (in the constructor)
        $this->isLoggedIn = true;
    }

    private function loginWithCookieData()
    {
        if (isset($_COOKIE['rememberme'])) {

            list ($id, $token, $hash) = explode(':', $_COOKIE['rememberme']);

            if ($hash == hash('sha256', $id . ':' . $token . COOKIE_SECRET_KEY) && !empty($token)) {

                if ($this->databaseConnection()) {

                    // get real token from database (and all other data)
                    $sth = $this->db_connection->prepare("SELECT id, username, email, first, last, accountType, isAdmin, readEULA FROM users WHERE id = :id
                                                      AND rememberMeToken = :rememberMeToken AND rememberMeToken IS NOT NULL");
                    $sth->bindValue(':id', $id, PDO::PARAM_INT);
                    $sth->bindValue(':rememberMeToken', $token, PDO::PARAM_STR);
                    $sth->execute();
                    // get result row (as an object)
                    $result_row = $sth->fetchObject();

                    if (isset($result_row->id)) {

                        // write user data into PHP SESSION [a file on your server]
                        $_SESSION['id'] = $result_row->id;
                        $_SESSION['username'] = $result_row->username;
                        $_SESSION['email'] = $result_row->email;
                        $_SESSION['first'] = $result_row->first;
                        $_SESSION['last'] = $result_row->last;
                        $_SESSION['accountType'] = $result_row->accountType;
                        $_SESSION['isAdmin'] = $result_row->isAdmin;
                        $_SESSION['isSuperUser'] = $result_row->isSuperUser;
                        $_SESSION['readEULA'] = $result_row->readEULA;
                        $_SESSION['isLoggedIn'] = 1;

                        // declare user id, set the login status to true
                        $this->id = $result_row->id;
                        $this->username = $result_row->username;
                        $this->email = $result_row->email;
                        $this->first = $result_row->first;
                        $this->last = $result_row->last;
                        $this->accountType = $result_row->accountType;
                        $this->isAdmin = $result_row->isAdmin;
                        $this->isSuperUser = $result_row->isSuperUser;
                        $this->readEULA = $result_row->readEULA;
                        $this->isLoggedIn = true;

                        // Cookie token usable only once
                        $this->newRememberMeCookie();
                        return true;
                    }
                }
            }

            // A cookie has been used but is not valid... we delete it
            $this->deleteRememberMeCookie();
            $this->errors[] = "Invalid cookie";
        }
        return false;
    }

    private function loginWithPostData()
    {
        // if POST data (from login form) contains non-empty username and non-empty user_password
        if (!empty($_POST['username']) && !empty($_POST['password'])) {

            // database query, getting all the info of the selected user
            $result_row = $this->getUserData(trim($_POST['username']));

            // if this user exists
            if (isset($result_row->id)) {

                // using PHP 5.5's password_verify() function to check if the provided passwords fits to the hash of that user's password
                if (password_verify($_POST['password'], $result_row->passwordHash)) {

                    if($result_row->accountDisabled == 0){

                      if ($result_row->userActive == 1) {

                          // write user data into PHP SESSION [a file on your server]
                          $_SESSION['id'] = $result_row->id;
                          $_SESSION['username'] = $result_row->username;
                          $_SESSION['email'] = $result_row->email;
                          $_SESSION['first'] = $result_row->first;
                          $_SESSION['last'] = $result_row->last;
                          $_SESSION['accountType'] = $result_row->accountType;
                          $_SESSION['isAdmin'] = $result_row->isAdmin;
                          $_SESSION['isSuperUser'] = $result_row->isSuperUser;
                          $_SESSION['readEULA'] = $result_row->readEULA;
                          $_SESSION['isLoggedIn'] = 1;

                          // declare user id, set the login status to true
                          $this->id = $result_row->id;
                          $this->username = $result_row->username;
                          $this->email = $result_row->email;
                          $this->first = $result_row->first;
                          $this->last = $result_row->last;
                          $this->accountType = $result_row->accountType;
                          $this->isAdmin = $result_row->isAdmin;
                          $this->isSuperUser = $result_row->isSuperUser;
                          $this->readEULA = $result_row->readEULA;
                          $this->isLoggedIn = true;

                          // if user has check the "remember me" checkbox, then generate token and write cookie
                          if (isset($_POST['rememberme'])) {

                              $this->newRememberMeCookie();

                          } else {

                              // Reset rememberme token
                              $this->deleteRememberMeCookie();

                          }

                          // OPTIONAL: recalculate the user's password hash
                          // DELETE this if-block if you like, it only exists to recalculate users's hashes when you provide a cost factor,
                          // by default the script will use a cost factor of 10 and never change it.
                          // check if the have defined a cost factor in config/hashing.php
                          if (defined('HASH_COST_FACTOR')) {

                              // check if the hash needs to be rehashed
                              if (password_needs_rehash($result_row->passwordHash, PASSWORD_DEFAULT, array('cost' => HASH_COST_FACTOR))) {

                                  // calculate new hash with new cost factor
                                  $this->passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT, array('cost' => HASH_COST_FACTOR));

                                  // TODO: this should be put into another method !?
                                  $query_update = $this->db_connection->prepare('UPDATE users SET passwordHash = :passwordHash WHERE id = :id');
                                  $query_update->bindValue(':passwordHash', $this->passwordHash, PDO::PARAM_STR);
                                  $query_update->bindValue(':id', $this->id, PDO::PARAM_INT);
                                  $query_update->execute();

                                  if ($query_update->rowCount() == 0) {
                                      // writing new hash was successful. you should now output this to the user ;)
                                  } else {
                                      // writing new hash was NOT successful. you should now output this to the user ;)
                                  }

                              }

                          }
                          // TO CLARIFY: in future versions of the script: should we rehash every hash with standard cost factor
                          // when the HASH_COST_FACTOR in config/hashing.php is commented out ?

                      } else {
                          $this->errors[] = "Your account is not activated yet. Please click on the confirm link in the mail.";
                      }

                    }else{
                      $this->errors[] = "Your account has been disabled. Please contact the system administrator.";
                    }



                } else {

                    $this->errors[] = "Wrong password. Try again.";

                }

            } else {

                $this->errors[] = "This user does not exist.";
            }

        } elseif (empty($_POST['username'])) {

            $this->errors[] = "username field was empty.";

        } elseif (empty($_POST['password'])) {

            $this->errors[] = "Password field was empty.";
        }

    }

    /**
     * Create all data needed for remember me cookie connection on client and server side
     */
    private function newRememberMeCookie()
    {
        // if database connection opened
        if ($this->databaseConnection()) {
            // generate 64 char random string and store it in current user data
            $random_token_string = hash('sha256', mt_rand());
            $sth = $this->db_connection->prepare("UPDATE users SET rememberMeToken = :rememberMeToken WHERE id = :id");
            $sth->execute(array(':rememberMeToken' => $random_token_string, ':id' => $_SESSION['id']));

            // generate cookie string that consists of userid, randomstring and combined hash of both
            $cookie_string_first_part = $_SESSION['id'] . ':' . $random_token_string;
            $cookie_string_hash = hash('sha256', $cookie_string_first_part . COOKIE_SECRET_KEY);
            $cookie_string = $cookie_string_first_part . ':' . $cookie_string_hash;

            // set cookie
            setcookie('rememberme', $cookie_string, time() + COOKIE_RUNTIME, "/", COOKIE_DOMAIN);
        }
    }

    /**
     * Delete all data needed for remember me cookie connection on client and server side
     */
    private function deleteRememberMeCookie()
    {
        // if database connection opened
        if ($this->databaseConnection()) {
            // Reset rememberme token
            $sth = $this->db_connection->prepare("UPDATE users SET rememberMeToken = NULL WHERE id = :id");
            $sth->execute(array(':id' => $_SESSION['id']));
        }

        // set the rememberme-cookie to ten years ago (3600sec * 365 days * 10).
        // that's obivously the best practice to kill a cookie via php
        // @see http://stackoverflow.com/a/686166/1114320
        setcookie('rememberme', false, time() - (3600 * 3650), '/', COOKIE_DOMAIN);
    }

    /**
     * perform the logout
     */
    public function doLogout()
    {
        $this->deleteRememberMeCookie();

        $_SESSION = array();
        session_destroy();

        $this->isLoggedIn = false;
        $this->messages[] = "You have been logged out.";
    }

    /**
     * simply return the current state of the user's login
     * @return boolean user's login status
     */
    public function isUserLoggedIn()
    {
        return $this->isLoggedIn;
    }

    /**
     * simply return the current admin status of the user
     * @return boolean users admin status
    */
    public function isUserAdmin()
    {
	    return $this->isAdmin;
    }

    /**
     * edit the user's name, provided in the editing form
     */
    public function editUsername()
    {
        if (!empty($_POST['username']) && $_POST['username'] == $_SESSION["username"]) {

            $this->errors[] = "Sorry, that username is the same as your current one. Please choose another one.";

        // username cannot be empty and must be azAZ09 and 2-64 characters
        // TODO: maybe this pattern should also be implemented in Registration.php (or other way round)
        } elseif (!empty($_POST['username']) && preg_match("/^(?=.{2,64}$)[a-zA-Z][a-zA-Z0-9]*(?: [a-zA-Z0-9]+)*$/", $_POST['username'])) {

            // escapin' this
            $this->username = substr(trim($_POST['username']), 0, 64);
            $this->id = intval($_SESSION['id']);

            // check if new username already exists
            $result_row = $this->getUserData($this->username);

            if (isset($result_row->id)) {

                $this->errors[] = "Sorry, that username is already taken. Please choose another one.";

            } else {

                // write user's new data into database
                $query_edit_username = $this->db_connection->prepare('UPDATE users SET username = :username WHERE id = :id');
                $query_edit_username->bindValue(':username', $this->username, PDO::PARAM_STR);
                $query_edit_username->bindValue(':id', $this->id, PDO::PARAM_INT);
                $query_edit_username->execute();

                if ($query_edit_username->rowCount()) {

                    $_SESSION['username'] = $this->username;
                    $this->messages[] = "Your username has been changed successfully. New username is " . $this->username . ".";

                } else {

                    $this->errors[] = "Sorry, your chosen username renaming failed.";

                }

            }

        } else {

            $this->errors[] = "Sorry, your chosen username does not fit into the naming pattern.";

        }

    }

    /**
     * edit the user's email, provided in the editing form
     */
    public function editUserEmail()
    {
        if (!empty($_POST['email']) && $_POST['email'] == $_SESSION["email"]) {

            $this->errors[] = "Sorry, that email address is the same as your current one. Please choose another one.";

        // user mail cannot be empty and must be in email format
        } elseif (!empty($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {

            // if database connection opened
            if ($this->databaseConnection()) {

                // prevent database flooding
                $this->email = substr(trim($_POST['email']), 0, 64);
                // not really necessary, but just in case...
                $this->id = intval($_SESSION['id']);

                // write users new data into database
                $query_edit_email = $this->db_connection->prepare('UPDATE users SET email = :email WHERE id = :id');
                $query_edit_email->bindValue(':email', $this->email, PDO::PARAM_STR);
                $query_edit_email->bindValue(':id', $this->id, PDO::PARAM_INT);
                $query_edit_email->execute();

                if ($query_edit_email->rowCount()) {

                    $_SESSION['email'] = $this->email;
                    $this->messages[] = "Your email address has been changed successfully. New email address is " . $this->email . ".";

                } else {

                    $this->errors[] = "Sorry, your email changing failed.";

                }

            }

        } else {

            $this->errors[] = "Sorry, your chosen email does not fit into the naming pattern.";

        }

    }

    /**
     * edit the user's password, provided in the editing form
     */
    public function editUserPassword()
    {
        if (empty($_POST['newPassword']) || empty($_POST['newPasswordRepeat']) || empty($_POST['oldPassword'])) {

            $this->errors[] = "Empty Password";

        } elseif ($_POST['newPassword'] !== $_POST['newPasswordRepeat']) {

            $this->errors[] = "Password and password repeat are not the same";

        } elseif (strlen($_POST['newPassword']) < 6) {

            $this->errors[] = "Password has a minimum length of 6 characters";

        // all the above tests are ok
        } else {

            // database query, getting hash of currently logged in user (to check with just provided password)
            $result_row = $this->getUserData($_SESSION['username']);

            // if this user exists
            if (isset($result_row->passwordHash)) {

                // using PHP 5.5's password_verify() function to check if the provided passwords fits to the hash of that user's password
                if (password_verify($_POST['oldPassword'], $result_row->passwordHash)) {

                    // now it gets a little bit crazy: check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                    // if so: put the value into $this->hash_cost_factor, if not, make $this->hash_cost_factor = null
                    $this->hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                    // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
                    // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
                    // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
                    // want the parameter: as an array with, currently only used with 'cost' => XX.
                    $this->passwordHash = password_hash($_POST['newPassword'], PASSWORD_DEFAULT, array('cost' => $this->hash_cost_factor));

                    // write users new hash into database
                    $query_update = $this->db_connection->prepare('UPDATE users SET passwordHash = :passwordHash WHERE id = :id');
                    $query_update->bindValue(':passwordHash', $this->passwordHash, PDO::PARAM_STR);
                    $query_update->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
                    $query_update->execute();

                    // check if exactly one row was successfully changed:
                    if ($query_update->rowCount()) {

                        $this->messages[] = "Password changed sucessfully!";

                    } else {

                        $this->errors[] = "Sorry, your password changing failed.";

                    }

                } else {

                    $this->errors[] = "Your OLD password was wrong.";

                }

            } else {

                $this->errors[] = "This user does not exist.";

            }

        }

    }

    /**
     *
     */
    public function setPasswordResetDatabaseTokenAndSendMail()
    {
        // set token (= a random hash string and a timestamp) into database, to see that THIS user really requested a password reset
        if ($this->setPasswordResetDatabaseToken() == true) {
            // send a mail to the user, containing a link with that token hash string
            $this->sendPasswordResetMail();
        }
    }

    /**
     *
     */
    public function setPasswordResetDatabaseToken()
    {
        if (empty($_POST['username'])) {

            $this->errors[] = "Empty username";

        } else {

            // generate timestamp (to see when exactly the user (or an attacker) requested the password reset mail)
            // btw this is an integer ;)
            $temporary_timestamp = time();

            // generate random hash for email password reset verification (40 char string)
            $this->passwordResetHash = sha1(uniqid(mt_rand(), true));

            $this->username = trim($_POST['username']);

            // database query, getting all the info of the selected user
            $result_row = $this->getUserData($this->username);

            // if this user exists
            if (isset($result_row->id)) {

                // database query:
                $query_update = $this->db_connection->prepare('UPDATE users SET passwordResetHash = :passwordResetHash,
                                                               passwordResetTimestamp = :passwordResetTimestamp
                                                               WHERE username = :username');
                $query_update->bindValue(':passwordResetHash', $this->passwordResetHash, PDO::PARAM_STR);
                $query_update->bindValue(':passwordResetTimestamp', $temporary_timestamp, PDO::PARAM_INT);
                $query_update->bindValue(':username', $this->username, PDO::PARAM_STR);
                $query_update->execute();

                // check if exactly one row was successfully changed:
                if ($query_update->rowCount() == 1) {

                    // define email
                    $this->email = $result_row->email;

                    return true;

                } else {

                    $this->errors[] = "Could not write token to database."; // maybe say something not that technical.

                }

            } else {

                $this->errors[] = "This username does not exist.";

            }

        }

        // return false (this method only returns true when the database entry has been set successfully)
        return false;
    }

    /**
     *
     */
    public function sendPasswordResetMail()
    {
        $mail = new PHPMailer;

        // please look into the config/config.php for much more info on how to use this!
        // use SMTP or use mail()
        if (EMAIL_USE_SMTP) {

            // Set mailer to use SMTP
            $mail->IsSMTP();
            //useful for debugging, shows full SMTP errors
            $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
            // Enable SMTP authentication
            $mail->SMTPAuth = EMAIL_SMTP_AUTH;
            // Enable encryption, usually SSL/TLS
            if (defined(EMAIL_SMTP_ENCRYPTION)) {
                $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
            }
            // Specify host server
            $mail->Host = EMAIL_SMTP_HOST;
            $mail->username = EMAIL_SMTP_USERNAME;
            $mail->Password = EMAIL_SMTP_PASSWORD;
            $mail->Port = EMAIL_SMTP_PORT;

        } else {

            $mail->IsMail();
        }

        $mail->From = EMAIL_PASSWORDRESET_FROM;
        $mail->FromName = EMAIL_PASSWORDRESET_FROM_NAME;
        $mail->AddAddress($this->email);
        $mail->Subject = EMAIL_PASSWORDRESET_SUBJECT;

        $link    = EMAIL_PASSWORDRESET_URL.'?u='.urlencode($this->username).'&v='.urlencode($this->passwordResetHash);
        $mail->Body = EMAIL_PASSWORDRESET_CONTENT. ' ' . $link . ' ';

        if(!$mail->Send()) {

            $this->errors[] = "Password reset mail NOT successfully sent! Error: " . $mail->ErrorInfo;
            return false;

        } else {

            $this->messages[] = "We sent you an email. Just click the link and you will be able to reset your password.";
            return true;
        }

    }

    /**
     *
     */
    public function checkIfEmailVerificationCodeIsValid()
    {
        if (!empty($_GET["u"]) && !empty($_GET["v"])) {

            // get username and password reset hash from url
            $this->username = trim($_GET['u']);
            $this->passwordResetHash = $_GET['v'];

            // database query, getting all the info of the selected user
            $result_row = $this->getUserData($this->username);
            print "hash: " . ($result_row->passwordResetHash === $this->passwordResetHash);

            // if this user exists and have the same hash in database
            if (isset($result_row->id) && $result_row->passwordResetHash == $this->passwordResetHash) {

                $timestamp_one_hour_ago = time() - 3600; // 3600 seconds are 1 hour

                if ($result_row->passwordResetTimestamp > $timestamp_one_hour_ago) {

                    // set the marker to true, making it possible to show the password reset edit form view
                    $this->password_reset_link_is_valid = true;

                } else {

                    $this->errors[] = "Your reset link has expired. Please use the reset link within one hour.";

                }

            } else {

                $this->errors[] = "This username does not exist.";

            }

        } else {

            $this->errors[] = "Empty link parameter data.";

        }

    }

    /**
     *
     */
    public function editNewPassword()
    {
        // TODO: timestamp!

        if (!empty($_POST['username'])
            && !empty($_POST['passwordResetHash'])
            && !empty($_POST['newPassword'])
            && !empty($_POST['newPasswordRepeat'])) {

            if ($_POST['newPassword'] === $_POST['newPasswordRepeat']) {

                if (strlen($_POST['newPassword']) >= 6) {

                    // if database connection opened
                    if ($this->databaseConnection()) {

                        // escapin' this, additionally removing everything that could be (html/javascript-) code
                        $this->username                = trim($_POST['username']);
                        $this->passwordResetHash = $_POST['passwordResetHash'];

                        // no need to escape as this is only used in the hash function
                        $this->user_password = $_POST['newPassword'];

                        // now it gets a little bit crazy: check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                        // if so: put the value into $this->hash_cost_factor, if not, make $this->hash_cost_factor = null
                        $this->hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                        // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
                        // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
                        // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
                        // want the parameter: as an array with, currently only used with 'cost' => XX.
                        $this->passwordHash = password_hash($this->user_password, PASSWORD_DEFAULT, array('cost' => $this->hash_cost_factor));

                        // write users new hash into database
                        $query_update = $this->db_connection->prepare('UPDATE users SET passwordHash = :passwordHash,
                                                                      passwordResetHash = NULL, passwordResetTimestamp = NULL
                                                                      WHERE username = :username AND passwordResetHash = :passwordResetHash');
                        $query_update->bindValue(':passwordHash', $this->passwordHash, PDO::PARAM_STR);
                        $query_update->bindValue(':passwordResetHash', $this->passwordResetHash, PDO::PARAM_STR);
                        $query_update->bindValue(':username', $this->username, PDO::PARAM_STR);
                        $query_update->execute();

                        // check if exactly one row was successfully changed:
                        if ($query_update->rowCount() == 1) {

                            $this->passwordResetSuccessful = true;
                            $this->messages[] = "Password changed successfully!";

                        } else {

                            $this->errors[] = "Sorry, your password changing failed.";

                        }

                    }

                } else {

                    $this->errors[] = "Password too short, please request a new password reset.";

                }

            } else {

                $this->errors[] = "Passwords dont match, please request a new password reset.";

            }

        }

    }

    /**
     *
     * @return boolean
     */
    public function passwordResetLinkIsValid()
    {
        return $this->password_reset_link_is_valid;
    }

    /**
     *
     * @return boolean
     */
    public function passwordResetWasSuccessful()
    {
        return $this->passwordResetSuccessful;
    }

    /**
     *
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     *
     */
    public function getPasswordResetHash()
    {
        return $this->passwordResetHash;
    }

    public function updateEula(){

        // if database connection opened
        if ($this->databaseConnection()) {

            // database query, getting all the info of the selected user
            $update_eula = $this->db_connection->prepare('UPDATE users SET readEULA=1 WHERE id = :id');
            $update_eula->bindValue(':id', $_SESSION['id'], PDO::PARAM_STR);
            $update_eula->execute();
            $_SESSION['readEULA'] = '1';

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
