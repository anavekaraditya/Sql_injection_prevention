<?php 
  session_start(); 

  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
?>
<!DOCTYPE html>
<html>
<head>
	<title>SQL Injection Prevention</title>
	<link rel="stylesheet" type="text/css" href="css/stylelist.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <!--<script type="text/javascript" src="js/validation.js"></script> -->
</head>
<body>
<div class="header">
	<h2>Home Page</h2>
</div>
<div class="content">
  	<!-- notification message -->
  	<?php if (isset($_SESSION['success'])) : ?>
      <div class="error success" >
      	<h3>
          <?php 
          	echo $_SESSION['success']; 
          	unset($_SESSION['success']);
          ?>
      	</h3>
      </div>
  	<?php endif ?>

    <!-- logged in user information -->
    <?php  if (isset($_SESSION['username'])) : ?>
    	<center><p>Welcome <strong><?php echo $_SESSION['username']; ?></strong></p></center></br>
    	<center><p><button class="btn"><a href="list.php?logout='1'" style="color: white;text-decoration:none">Logout</a></button></p></center>
    <?php endif ?>
</div>
<form method="GET" action="list.php">
  	<div class="input-group">
  		<label>Search Bar</label>
  		<input type="text" name="search" id="search" required value="<?php if(isset($_GET['search'])){echo $_GET['search']; } ?>" class="form-control" placeholder="Search data"></br>
		<span id="lblError" style="color: red"></span><br>
  	</div>
  	<div class="input-group">
  		<button type="submit" class="btn">Search</button>
  	</div>
      <center>
      <table>
                            <thead>
                            <center><span id="lblError" style="color: red"></span></center><br>
                                <tr>
                                    <th>ID</th>
                                    <th>Categories</th>
                                    <th>Product_name</th>
                                    <th>Prize</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if(isset($_GET['search']))
                                    {
                                    $con = mysqli_connect("localhost","root","","sqlia");
                                    $item = mysqli_real_escape_string($con, $_GET['search']);
                                    $ip = $_SERVER['REMOTE_ADDR'];
                                    $ts = date("Y-m-d H:i:s");
                                    $pg = "list.html";
                                    $flag1 = false;
                                    $flag2 = false;
                                    $flag3 = false;
                                    // connect to the database
                                    $db = mysqli_connect('localhost', 'root', '', 'sqlia');
                                  
                                    $has_equ = strpos($item, '=') !== false;
                                    $has_exm = strpos($item, '!') !== false;
                                    $has_and = stripos($item, 'and') !== false;
                                    $has_or = stripos($item, 'or') !== false;
                                    $has_com1 = strpos($item, '"') !== false;
                                    $has_com2 = strpos($item, "'") !== false;
                                  
                                    if (($has_com2+$has_com1+$has_or+$has_and+$has_exm+$has_equ)!=0){
                                        $flag1 = true;
                                        $sql = "INSERT INTO detection_log (time, error_type, ip, page) VALUES('$ts', '1', '$ip','$pg')";
                                        mysqli_query($db, $sql);
                                    }
                                  
                                    $query = "SELECT * FROM product WHERE CONCAT(Categories,product_name,prize) LIKE '%$item%' ";
                                    $ele_of_words = explode("'",$query);
                                  
                                    if (count($ele_of_words)!=3){
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
                                  
                                    if ($flag1+$flag2+$flag3<2) { 
                                    

                                       $query = "SELECT * FROM product WHERE CONCAT(Categories,product_name,prize) LIKE '%$item%' ";
                
                                        $query_run = mysqli_query($con, $query);

                                        if(mysqli_num_rows($query_run) > 0)
                                        {
                                            foreach($query_run as $items)
                                            {
                                                ?>
                                                <tr>
                                                    <td><?= $items['id']; ?></td>
                                                    <td><?= $items['Categories']; ?></td>
                                                    <td><?= $items['product_name']; ?></td>
                                                    <td><?= $items['prize']; ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        /*else
                                        {
                                            ?>
                                                <tr>
                                                    <td colspan="4">No Record Found</td>
                                                </tr>
                                            <?php
                                        }*/
                                        }
                                    }
                                    
                                ?>
                            </tbody>
                            
                        </table>
                                </center>
  </form>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>		
</body>
</html>