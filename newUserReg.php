<?php
session_start();

date_default_timezone_set("Asia/Calcutta");

	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin_staff"]) || $_SESSION["loggedin_staff"] !== true) {
		header("location: login_staff.php");
		exit;
	}
	
	require 'connect_db.php';
	
	require 'mailDetails.php';
	
	//error variables
	$fname_err = $lname_err = $email_err = $phone_err =  $address_err = "";
	
	//value variables
	$fname = $lname = $email = $phone_num = $address = "";
	
	if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["new_user_create"])) {
		//checking if all all fields are filled and validated
		if(empty(trim($_POST["fname_txt"]))) {
			$fname_err = "Please enter a first name";
		} else {
			$fname = trim($_POST['fname_txt']);
			if (!preg_match ("/^[a-zA-z]*$/", $fname) ) {  
				$fname_err = "Only alphabets and whitespace are allowed.";  
			}
		}
		
		if(empty(trim($_POST['lname_txt']))) {
			$lname_err = "Please enter a first name";
		} else {
			$lname = trim($_POST['lname_txt']);
			if (!preg_match ("/^[a-zA-z]*$/", $lname) ) {  
				$lname_err = "Only alphabets and whitespace are allowed.";  
			}
		}
		
		if(empty(trim($_POST['email_txt']))) {
			$email_err = "Please enter a first name";
		} else {
			$email = trim($_POST['email_txt']);
			$pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";  
			if (!preg_match ($pattern, $email)) {  
				$email_err = "Email is not valid.";  
            }  
		}
		
		if(empty(trim($_POST['phone_num_txt']))) {
			$phone_err = "Please enter a first name";
		} else {
			$phone_num = trim($_POST['phone_num_txt']);
			$length = strlen($phone_num); 
			if ($length<10 && $length>10) {  
				$phone_err = "Mobile must have 10 digits";
			}
		}
		
		if(empty(trim($_POST['address_text']))) {
			$address_err = "Please enter a first name";
		} else {
			$address = trim($_POST['address_text']);
		}
		 
		if((empty($fname_err)) && (empty($lname_err)) && (empty($email_err)) && (empty($phone_err)) && (empty($address_err))) {
			//all values are entered properly.
			//generate password
			$bytes = openssl_random_pseudo_bytes(2);
			$pwd = bin2hex($bytes);
			$reader_ID = "";
			$staff_ID = $_SESSION["id"];
			//query to get latest reader_ID from reader2 table
			$get_lastID_Que = "SELECT max(reader_ID) last_ID FROM reader";
			
			$result = $link->query($get_lastID_Que);
			$row_num = mysqli_num_rows($result);
			
			if($row_num == 1) {
				while($data = $result->fetch_assoc()) {
					$reader_ID = intval($data['last_ID']);
					// echo "Last reader_ID was ".$reader_ID."";
				}
			} else {
				echo "Some error occurred";
			}
			//increment reader_ID so that we can insert new reader to that ID
			$reader_ID = $reader_ID + 1;
			$action = "New User Registration";
			$date1 =  date("Y-m-d");
			$date_temp = strtotime(date('Y-m-d'));
			$due_date = date('Y-m-d',strtotime('+15 days',$date_temp));
			$time1 = date("h:i:s");

			//query to insert into READER table
			$insert_readerQue = "INSERT INTO reader (reader_ID, fname, lname, email_ID, phone, address, staff_ID, login_ID) VALUES('".$reader_ID."', '".$fname."', '".$lname."', '".$email."','".$phone_num."','".$address."','".$staff_ID."','".$reader_ID."')";

			//query to insert into AUTH_NEW table. This has to be done first
			$insert_authQue = "INSERT INTO auth VALUES('".$reader_ID."', '".$pwd."')";

			//query to insert into REPORT table
			$insert_reportQue = "INSERT INTO report(reader_ID, action, date, time, staff_ID) VALUES('".$reader_ID."', '".$action."', '".$date1."', '".$time1."', '".$staff_ID."')";
			
			//executing queries
			//inserting into AUTH table. This has to be done first
			if ($link->query($insert_authQue) === TRUE) {
				//echo "New record created successfully";
				//we do nothing here
			} else {
			echo "Error: " . $insert_authQue . "<br>" . $link->error;
			}
			
			//inserting into READER table
			if ($link->query($insert_readerQue) === TRUE) {
				//echo "New record created successfully";
				//we do nothing here
			} else {
			echo "Error: " . $insert_readerQue . "<br>" . $link->error;
			}
			
			//inserting into REPORT table
			if ($link->query($insert_reportQue) === TRUE) {
				//echo "New record created successfully";
				//we do nothing here
			} else {
			echo "Error: " . $insert_reportQue . "<br>" . $link->error;
			}

			//send mail to new user
			$mail->setFrom('biblio.dsce@gmail.com');
    		$mail->addAddress($email);
			$mail->isHTML(true);                                  //Set email format to HTML
    		$mail->Subject = 'Welcome To Biblio@DSCE';

			$mail->Body    = '<p>Hello '.$fname.', here is your login credentials.</p>
							  <p>Login ID : <b>'.$reader_ID.'</b><br/>
							  Password : <b>'.$pwd.'</p>
							  <p>We welcome you to our large family of book-lovers!</p>';

			$mail->AltBody = 'Here is your login credentials. Login ID : '.$reader_ID.'; Password : '.$pwd.'. Have a good day!';

			if(!$mail->send()) {
				// echo 'Message could not be sent.';
				// echo 'Mailer Error: ' . $mail->ErrorInfo; No need to print any message
			} else {
				//echo 'Message has been sent'; 
			}
			
			echo "<script>alert('Reader registered successfully. Password is ".$pwd."');document.location='welcome_staff.php';</script>";
		} else {
			//checking what error here. thats what we are getting. there's some error here boy
			echo "There's some error here boy";
			echo "<br/>Fname Error : ".$fname_err;
			echo "<br/>Lname Error : ".$lname_err;
			echo "<br/>Email Error : ".$email_err;
			echo "<br/>Phone No Error : ".$phone_err;
			echo "<br/>Address Error: ".$address_err;
		}
	}
?>


<html>
<head>
<title>Biblio@DSCE</title>
	
	<script src="https://kit.fontawesome.com/5d3eee0a99.js" crossorigin="anonymous"></script>
	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	
	
	<link rel="stylesheet" type="text/css" href="homeu.css">
	<style>
		.page-content td{
			vertical-align:middle;
		}
	</style>
</head>
<body>
		<div class="vertical-nav bg-dark text-light" id="sidebar">
			<div class="py-4 px-3 mb-4 bg-dark text-light">
				<div class="media d-flex align-item-center">
					<img src="avatar1.png" alt="user image" width="80" height="80" class="mr-3 rounded-circle img-thumbnail shadow-sm">
					<div class="media-body">
					<h4 class="mt-3"> <?php echo htmlspecialchars($_SESSION["username"]); ?> </h4>
					<p class="font-weight-normal text-muted mb-0">ADMIN</p>
					</div>
				</div>
			</div>
			
			<p class="text-gray font-weight-bold text-uppercase px-3 small pb-4 mb-0">Dashboard</p>
			<ul class="nav flex-column bg-white mb-0">
				<li class="nav-item">
					<a href="welcome_staff.php" class="nav-link bg-dark text-light"><i class="fa fa-th-large mr-3 text-primary fa-fw"></i>home</a>
				</li>
				<li class="nav-item">
					<a href="staffsearch.php" class="nav-link bg-dark text-light"><i class="fas fa-search mr-3 text-primary fa-fw"></i>search book</a>
				</li>
				<li class="nav-item">
					<a href="newbook.php" class="nav-link active bg-dark text-light"><i class="fas fa-plus-square mr-3 text-primary fa-fw"></i>add book</a>
				</li>
				<li class="nav-item">
					<a href="bookcatalogue.php" class="nav-link bg-dark text-light"><i class="fas fa-book-reader mr-3 text-primary fa-fw"></i>Book Catalogue</a>
				</li>
				<li class="nav-item">
					<a href="staffborrow.php" class="nav-link bg-dark text-light"><i class="fas fa-book-open mr-3 text-primary fa-fw"></i>issue book</a>
				</li>
				<li class="nav-item">
					<a href="outstandingpage.php" class="nav-link bg-dark text-light"><i class="fas fa-book mr-3 text-primary fa-fw"></i>outstanding books</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link bg-dark text-dark"  id="highlight"><i class="fas fa-user-plus mr-3 text-primary fa-fw"></i>register new user</a>
				</li>
			</ul>
			
			<p class="text-gray font-weight-bold text-uppercase px-3 small py-4 mb-0">Charts</p>
			<ul class="nav flex-column bg-white mb-0">
				<li class="nav-item">
					<a href="#" class="nav-link bg-dark text-light"><i class="fas fa-clipboard mr-3 text-primary fa-fw"></i>report</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link bg-dark text-light"><i class="fas fa-chart-bar mr-3 text-primary fa-fw"></i>statistics</a>
				</li>
			</ul>	
			
			<p class="text-gray font-weight-bold text-uppercase px-3 small py-4 mb-0"></p>
			<ul class="nav flex-column bg-white mb-0">
				<li class="nav-item">
					<a href="logout_staff.php" class="nav-link bg-dark text-light"><i class="fas fa-sign-out-alt mr-3 text-primary fa-fw"></i>logout</a>
				</li>
			</ul>
		
		</div>
		
		<div class="page-content p-5" id="content">
			<h1>Register New User</h1><br/>
			
			<form action="" method="post">
				<div class="row mb-3">
				<label class="col-sm-2 col-form-label">First Name : </label>
				<div class="col-sm-5"><input type="text" class="form-control" autocomplete="off" name="fname_txt"/></div>
				<span class="invalid-feedback"><?php echo $fname_err; ?></span>
				</div>
				<div class="row mb-3">
				<label class="col-sm-2 col-form-label">Last Name :</label>
				<div class="col-sm-5"><input type="text" class="form-control" autocomplete="off" name="lname_txt"/></div>
				<span class="invalid-feedback"><?php echo $lname_err; ?></span>
				</div>
				<div class="row mb-3">
				<label class="col-sm-2 col-form-label">Email Address :</label>
				<div class="col-sm-5"><input type="email" name="email_txt" autocomplete="off" class="form-control"/></div>
				<span class="invalid-feedback"><?php echo $email_err; ?></span>
				</div>
				<div class="row mb-3">
				<label class="col-sm-2 col-form-label"> Phone Number :</label>
				<div class="col-sm-5"><input type="number" name="phone_num_txt" autocomplete="off" class="form-control"/></div>
				<span class="invalid-feedback"><?php echo $phone_err; ?></span>
				</div>
				<div class="row mb-3">
				<label class="col-sm-2 col-form-label"> Address : </label>
				<div class="col-sm-5"><input type="text" class="form-control" autocomplete="off" name="address_text"/></div>
				<span class="invalid-feedback"><?php echo $address_err; ?></span>
				</div>
				<br/>
				<input type="submit" class="btn btn-primary" name="new_user_create" value="Create User"/>
				</form>
			
			
		</div>
</body>
</html>