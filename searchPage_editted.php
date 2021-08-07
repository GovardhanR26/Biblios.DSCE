<?php
session_start();

date_default_timezone_set("Asia/Calcutta");

	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
		header("location: login_reader.php");
		exit;
	}
	
	require 'connect_db.php';
	require 'mailDetails.php';
	
	// checking if any button in the table has been pressed
	if(($_SERVER["REQUEST_METHOD"]=="POST") && (isset($_POST["table_button"]))) {
		$issueID = $_POST["table_button"];
		echo "You want ".$issueID." ?";
		
		// code paste
		$reader_ID = $_SESSION["id"];
		$book_ID = $issueID;
		$action = "Borrow";
		$date1 =  date("Y-m-d");
		$time1 = date("h:i:s");
		$date_temp = strtotime(date('Y-m-d'));
		$due_date = date('Y-m-d',strtotime('+15 days',$date_temp));
			
		// we should check if the book is available first. if not show message.
		$checkQue = "SELECT availability FROM book where book_ID = ?";
		if($stmt = mysqli_prepare($link, $checkQue)){
				
			mysqli_stmt_bind_param($stmt, "s", $param_bookID);
			$param_bookID = $book_ID;
				
			if(mysqli_stmt_execute($stmt)){
					
				mysqli_stmt_store_result($stmt);
					
				if(mysqli_stmt_num_rows($stmt) == 1) {
						
					mysqli_stmt_bind_result($stmt, $availability);
						
					if(mysqli_stmt_fetch($stmt)){
							
						if($availability == 'Available') {
							//insert into REPORT table
							$insert_reportQue = "INSERT INTO report(reader_ID, book_ID, action, date, time) VALUES('".$reader_ID."','". $book_ID."','".$action."','".$date1."','".$time1."')";
								
							//update 'Available' to 'Borrowed'
							$updateQue = "UPDATE book SET availability='Borrowed' where book_ID='".$book_ID."'";
								
							//insert into BORROWED table
							$insert_borrowQue = "INSERT INTO borrowed VALUES('".$reader_ID."','".$book_ID."','".$date1."','".$due_date."')";
							
							//getting book title and author; for mail purpose
							$get_Que = "SELECT title, author FROM book WHERE book_ID=".$book_ID."";
							$result = $conn->query($get_Que);

							if ($result->num_rows > 0) {
								// output data of each row
								while($row = $result->fetch_assoc()) {
									$book_title = $row["title"];
									$book_author = $row["author"];
								}

								//query to get email_id of reader
								$mail_Que = "SELECT email_ID from reader WHERE reader_ID=".$reader_ID.""; //working here
							} else {
								//echo "0 results";
							}
								
							//execute queries
							//inserting into BORROWED table
							if ($link->query($insert_borrowQue) === TRUE) {
								//echo "New record created successfully";
								//we do nothing here
							} else {
								echo "Error: " . $insert_reserveQue . "<br>" . $link->error;
							}
								
							//inserting into REPORT table
							if ($link->query($insert_reportQue) === TRUE) {
								//echo "New record created successfully";
								//we do nothing here
							} else {
								echo "Error: " . $insert_reportQue . "<br>" . $link->error;
							}
								
							//updating the BOOK table
							if ($link->query($updateQue) === TRUE) {
								//echo "New record created successfully";
								//we do nothing here
							} else {
								echo "Error: " . $updateQue . "<br>" . $link->error;
							}
							//send mail to new user
							$mail->setFrom('biblio.dsce@gmail.com');
							$mail->addAddress($email);
							$mail->isHTML(true);                                  //Set email format to HTML
							$mail->Subject = 'Book Issue';

							$mail->Body = '<p>Hello '.$fname.', here is your login credentials.</p>
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

							echo "<script>alert('Book issued successfully');document.location='welcome_reader.php'</script>";
							//javascript alert code here
							//header("Location: welcome_reader.php");
							//exit;
						} else if($availability == 'Borrowed') {
							//reserving code here
							$action = "Reserve";
							
							//insert into REPORT table
							$insert_reportQue = "INSERT INTO report(reader_ID, book_ID, action, date, time) VALUES('".$reader_ID."','". $book_ID."','".$action."','".$date1."','".$time1."')";
								
							//update book availability to 'Reserved'
							$updateQue = "UPDATE book SET availability='Reserved' where book_ID='".$book_ID."'";
								
							//insert into RESERVED table
							$insert_reserveQue = "INSERT INTO reserved VALUES('".$reader_ID."','".$book_ID."','".$date1."','".$time1."')";
								
							//execute queries
							//inserting into BORROWED table
							if ($link->query($insert_reserveQue) === TRUE) {
								//echo "New record created successfully";
								//we do nothing here
							} else {
								echo "Error: " . $insert_reserveQue . "<br>" . $link->error;
							}
								
							//inserting into REPORT table
							if ($link->query($insert_reportQue) === TRUE) {
								//echo "New record created successfully";
								//we do nothing here
							} else {
								echo "Error: " . $insert_reportQue . "<br>" . $link->error;
							}
								
							//updating the BOOK table
							if ($link->query($updateQue) === TRUE) {
								//echo "New record created successfully";
								//we do nothing here
							} else {
								echo "Error: " . $updateQue . "<br>" . $link->error;
							}
							echo "<script>alert('Book reserved successfully');document.location='welcome_reader.php'</script>";
								
						} else {
							//if book is already reserved
							echo "Book is temporarily unavailable<br/>";
							$issue_err = "Book is temporarily unavailable";
						}
					}
				} else {
					echo "Book not found <br/>";
					$issue_err = "Book not found";
				}
			}
		}
		
		//pasted code end
		
	}
		

?>
<html>
<head>
<title> Search Page </title>
</head>
<body>
<div>
	<form action="" method="post">
	<p>
	<label> Search goes here
	<input type="text" id="search_bar"name="search_txt"/>
	</label>
	</p>
	<input type="submit" name="search_submit" value="Search"/>
	</form>
	<?php
		if(($_SERVER["REQUEST_METHOD"]=="POST") && (isset($_POST["search_submit"]))) {
			$search_txt = $_POST["search_txt"];
			echo "<script>getElementById('search_bar').innerHTML='".$search_txt."';</script>";
			if(!empty($search_txt)) {
				$searchQue = "SELECT * FROM book WHERE title LIKE '%".$search_txt."%' OR author LIKE '%".$search_txt."%'";
			
				$result = $link->query($searchQue);
			
				if($result == True) {
					$row_num = mysqli_num_rows($result);
					if($row_num>0) {
						//display in html table
						?>
						<table border="1" cellpadding="10">
						<tr>
							<th> Book ID </th>
							<th> Title </th>
							<th> Author </th>
							<th colspan="2"> Availability </th>
						</tr>
						<?php
						while($row=$result->fetch_assoc()) 
						{
						?>
							<tr>
								<td><?php echo $row['book_ID']?></td>
								<td><?php echo $row['title']?></td>
								<td><?php echo $row['author']?></td>
								<td><?php echo $row['availability']?></td>
								<td><form action="" method="post">
								<button name="table_button" type="submit" value="<?php echo $row['book_ID']; ?>" <?php if(($row['availability']!='Available')&&($row['availability']!='Borrowed')) {?> disabled <?php } ?>>
								<?php 
									if($row['availability']=='Available') {
										echo "Issue";
									} else if($row['availability']=='Borrowed') {
										echo "Reserve";
									} else if(($row['availability']=='Available_Reserved')|| ($row['availability']=='Reserved')) {
										echo "Reserved";
									} else {
										echo "Unavailable";
									}
								?> 
								</button>
								</form>
								</td>
							</tr>
						<?php
						}
						?>
							</table>
						<?php
					}
				}
			} else {
				echo "Please enter text";
			}				
		} else {
				//echo "Got false bro";
		}		
	?>
	<a href="welcome_reader.php">Back</a>
</div>
</body>
</html>