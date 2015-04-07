<?php
/*
 * Cru Doctrine
 * Login
 * Campus Crusade for Christ
 */

try {
  //get values
  $submit     = isset($_POST['submit'])      ? true                  : false;
  $ajax       = isset($_POST['ajax'])        ? true                  : false;

  $email      = isset($_POST['email'])       ? $_POST['email']       : '';
  $password   = isset($_POST['password'])    ? $_POST['password']    : '';
  $rememberMe = isset($_POST['rememberMe'])  ? $_POST['rememberMe']  : false;
  $redir      = isset($_POST['redir'])       ? $_POST['redir']       : '';

  $errors     = isset($_POST['errors'])      ? $_POST['errors']      : '';

  require_once("config.inc.php"); 
  require_once("Database.singleton.php");

  //check for form submission
  if($submit) { //form was submitted, process data
    //initialize the database object
    $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
    $db->connect();
    
    $sql = "SELECT * FROM user WHERE Email = '".$db->escape($email)."'";
    //get results
    $result = null;
    $result = $db->query_first($sql);
    $storedPassword = null;
    $storedPassword = $result['Password'];

    //hash the supplied password with some salt
    $passwordHash = null;
    $passwordHash = hash("sha512", $password.$email);

    //check result to verify login
    if($storedPassword == $passwordHash) { //success
      //log user in
      session_start();
      $_SESSION['email']  = $email;
      $_SESSION['fname']  = $result['FName'];
      $_SESSION['lname']  = $result['LName'];
      $_SESSION['type']   = $result['Type'];
      $_SESSION['region'] = $result['Region'];
      $_SESSION['status'] = $result['Status'];

      //$_SESSION['documentRoot']  = $_SERVER['REQUEST_URI'];

      if($rememberMe) {
        $year = time() + 31536000;
        setcookie('remember_me', $email, $year);
      }
      elseif(!$rememberMe) {
        if(isset($_COOKIE['remember_me'])) {
          $past = time() - 100;
          setcookie(remember_me, gone, $past);
        }
      }

      //if ajax, return user attributes as xml
      if ($ajax) {
        header ("Location: /");
      }
      else {
        header ("Location: /");
      }
    }
    else { //fail
      //return errors
      $errors .= 'Login failed. Please check your email and password.';

      //if ajax, return error
      if ($ajax) {
        echo 'error';
        exit();
      }
    }
    $db->close();
  }
} 
catch (PDOException $e) {
  echo $e->getMessage();
  exit();
}
?>