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
<title> New User Registration </title>
</head>
<body>
	<form action="" method="post">
	<label> First Name : 
	<input type="text" name="fname_txt"/>
	<span class="invalid-feedback"><?php echo $fname_err; ?></span>
	</label><br/>
	<label> Last Name :
	<input type="text" name="lname_txt"/>
	<span class="invalid-feedback"><?php echo $lname_err; ?></span>
	</label><br/>
	<label> Email Address :
	<input type="email" name="email_txt"/>
	<span class="invalid-feedback"><?php echo $email_err; ?></span>
	</label><br/>
	<label> Phone Number :
	<input type="number" name="phone_num_txt"/>
	<span class="invalid-feedback"><?php echo $phone_err; ?></span>
	</label><br/>
	<label> Address :
	<input type="text" name="address_text"/>
	<span class="invalid-feedback"><?php echo $address_err; ?></span>
	</label><br/>
	<br/>
	<input type="submit" name="new_user_create" value="Create User"/>
	</form>
	<p>
		<a href="welcome_staff.php">Back</a>
	</p>
</body>
</html>
