<?php
session_start();

// initializing variables
$username = "";
$usermatch = "";
$email    = "";
$password = "";
$password_1 = "";
$errors = array(); 
$sqlia = array();
$flag1 = false;
$flag2 = false;
$flag3 = false;
// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'sqlia');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = stripcslashes($username);
  $email = stripcslashes($email);
  $password_1 = stripcslashes($password_1);
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $usermatch = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }
  //$email = md5($email);
  //$username = md5($username);
  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1);//encrypt the password before saving in the database
  	$query = "INSERT INTO users (username, email, password) 
  			  VALUES('$username', '$email', '$password')";
  	mysqli_query($db, $query);
  	$_SESSION['username'] = $usermatch;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: list.php');
  }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = stripcslashes($username);
  $password = stripcslashes($password);
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  $usermatch = mysqli_real_escape_string($db, $_POST['username']);

  if (empty($username)) {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }

  $ip = $_SERVER['REMOTE_ADDR'];
  $ts = date("Y-m-d H:i:s");
  $pg = "login.html";
  $has_equ = strpos($username, '=') !== false;
  $has_exm = strpos($username, '!') !== false;
  $has_and = stripos($username, 'and') !== false;
  $has_or = stripos($username, 'or') !== false;
  $has_com1 = strpos($username, '"') !== false;
  $has_com2 = strpos($username, "'") !== false;

  if (($has_com2+$has_com1+$has_or+$has_and+$has_exm+$has_equ)!=0){
    $flag1 = true;
    $sql = "INSERT INTO detection_log (time, error_type, ip, page) VALUES('$ts', '1', '$ip','$pg')";
    mysqli_query($db, $sql);
}
function word_count($q){
  return count(explode("'",$q));
}

$original_query = "SELECT * FROM users WHERE username='username' AND password='password'";
$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
print_r($query);
$ele_of_words = explode("'",$query);

if (count($ele_of_words) != word_count($original_query)){
    $flag2 = true;
    $sql = "INSERT INTO detection_log (time, error_type, ip, page) VALUES('$ts', '2', '$ip','$pg')";
    mysqli_query($db, $sql);
}

$results = mysqli_query($db, $query);
if (mysqli_num_rows($results) > 1){
  $flag3 = true;
  $sql = "INSERT INTO detection_log (time, error_type, ip, page) VALUES('$ts', '3', '$ip','$pg')";
  mysqli_query($db, $sql);
}

  if (count($errors) == 0 && $flag1+$flag2+$flag3<2) {
  	$password = md5($password);
    $username = $username;
  	$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
      if($username == $usermatch){
        $_SESSION['username'] = $usermatch;
        $_SESSION['success'] = "You are now logged in";
        header('location: list.php');
      }
  	  
  	}else {
  		array_push($errors, "Wrong username/password combination");
  	}
  }

}

?>