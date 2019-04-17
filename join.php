<?php 
/* ---------------------------------------------------------------------------
 * filename    : fr_per_create2.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program adds/inserts a new volunteer (table: fr_persons)
 * ---------------------------------------------------------------------------
 */
//session_start();
// session_start();
// if(!isset($_SESSION["id"])){ // if "user" not set,
	// session_destroy();
	// header('Location: login.php');     // go to login page
	// exit;
// }
	
require 'database.php';
if ( !empty($_POST)) { // if not first time through
	// initialize user input validation variables
	$emailError = null;
	$passwordError = null;

	// initialize $_POST variables
	$email = $_POST['email'];
	$password = $_POST['password'];
	$passwordhash = MD5($password);
	
	// validate user input
	$valid = true;
	// do not allow 2 records with same email address!
	if (empty($email)) {
		$emailError = 'Please enter valid Email Address (REQUIRED)';
		$valid = false;
	} else if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
		$emailError = 'Please enter a valid Email Address';
		$valid = false;
	}
	$pdo = Database::connect();
	$sql = "SELECT * FROM Users";
	foreach($pdo->query($sql) as $row) {
		if($email == $row['email']) {
			$emailError = 'Email has already been registered!';
			$valid = false;
		}
	}
	Database::disconnect();
	
	// email must contain only lower case letters
	if (strcmp(strtolower($email),$email)!=0) {
		$emailError = 'email address can contain only lower case letters';
		$valid = false;
	}
	
	//validate password
	if (empty($password)) {
		$passwordError = 'Please enter valid Password';
		$valid = false;
	}
	// insert data
	if ($valid) 
	{
		$pdo = Database::connect();
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO `Users` (email,password_hashed) values(?, ?)";
		$q = $pdo->prepare($sql);
		$q->execute(array($email,$passwordhash));
		Database::disconnect();
		header("Location: login.php");
	}
}
?>
<!DOCTYPE html>
        <html>
            <head>
                <title>Add a new User</title>
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                <style>label {width: 5em;}</style>
                   </head>
                   <body>
                <div class='container'>
                    <div class='span10 offset1'>
                        <p class='row'>
                            <h3>Add a New User</h3>
                        </p>
                        <form class='form-horizontal' action="join.php" method="post" enctype="multipart/form-data">     
				<div class="control-group <?php echo !empty($emailError)?'error':'';?>">
					<label class="control-label">Email</label>
					<div class="controls">
						<input name="email" type="text" placeholder="Email" value="<?php echo !empty($email)?$email:'';?>">
						<?php if (!empty($emailError)): ?>
							<span class="help-inline"><?php echo $emailError;?></span>
						<?php endif;?>
					</div>
				</div>
				<br>
						
				<div class="control-group <?php echo !empty($passwordError)?'error':'';?>">
					<label class="control-label">Password</label>
					<div class="controls">
						<input id="password" name="password" type="password"  placeholder="password" value="<?php echo !empty($password)?$password:'';?>">
						<?php if (!empty($passwordError)): ?>
							<span class="help-inline"><?php echo $passwordError;?></span>
						<?php endif;?>
					</div>
				</div>
				<br>
				<div class='form-actions'>
                                <button type="submit" class="btn btn-success">Confirm</button>
                                <a class='btn btn-secondary' href='login.php'>Back</a>
                            </div>
                        </form>
                    </div>

                </div> <!-- /container -->
            </body>
        </html>