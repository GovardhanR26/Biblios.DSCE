<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin_staff"]) && $_SESSION["loggedin_staff"] === true) {
    header("location: welcome_staff.php");
    exit;
}

// Include config file
require_once "connect_db.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    //echo "You entered : ".$username." : ".$password."";
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        //$sql = "SELECT auth.loginID, auth.password, st.name FROM authentication auth, staff st WHERE auth.loginID = st.loginID and auth.loginID = ?";
        $sql = "SELECT a.login_ID userID, a.password password, r.fname FROM staff r, auth a WHERE r.login_ID = a.login_ID AND a.login_ID = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $hashed_password, $sname);
                    if(mysqli_stmt_fetch($stmt)){
                        //if(password_verify($password, $hashed_password)){
						if($password==$hashed_password) {
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin_staff"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $sname;                            
                            
                            // Redirect user to welcome page
                            header("location: welcome_staff.php");
                        } else{
                            // Password is not valid, display a generic error message
							//I TYPED
							//echo "<br/>Invalid username. password mismatch<br/>";
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
					//I TYPED
					//echo "<br/>Invalid username. Number of rows not one<br/>";
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Biblio@DSCE</title>
	<script src="https://kit.fontawesome.com/5d3eee0a99.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<style>
@import url('https://fonts.googleapis.com/css2?family=Baloo+Chettan+2:wght@600&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@500&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@600&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC:wght@800&display=swap');
</style>
    <link rel="stylesheet" type="text/css" href="styletest.css">
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-custom navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="font-family: 'Segoe Script';">Biblio@DSCE</a>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="logintest.php">User</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Admin</a>
        </li>
		</ul>
    </div>
  </div>
</nav>





    <div class="loginbox">
	
	<h1>Login</h1>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            
                <p><i class="far fa-user"></i>Username</p>
				<input type="text" placeholder="Enter Username" name="username" autocomplete="off" class="<?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><i class="fas fa-exclamation-circle"></i><?php echo $username_err; ?></span>
             
            
                <p><i class="fas fa-unlock"></i>Password</p>
				<input type="password" placeholder="Enter Password" name="password" class="<?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><i class="fas fa-exclamation-circle"></i><?php echo $password_err; ?></span>
           
            <div class="form-group">
                <input type="submit" value="Login"></br>
            </div>
            <!--Not currently using Sign Up option<p>Don't have an account? <a href="register.php">Sign up now</a>.</p>-->
        </form>
    </div>
	<div class="invalidmsg">
		<?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i>' . $login_err . '</div>';
        }        
        ?>
	</div>
</body>
</html>