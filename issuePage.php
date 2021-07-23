<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login_reader.php");
    exit;
}
	$issue_err = "";
	require 'connect_db.php';	
	
	if($_SERVER["REQUEST_METHOD"]=="POST") {
		$issueID = test_input($_POST["issueBookID"]);
		
		if($issueID==NULL) {
			echo "<br>Search field is empty<br>";
		}
		else {
			require 'connect_db.php';
			$userID = $_SESSION["id"];
			$bookID = $issueID;
			$action = "Borrow";
			$date1 =  date("Y-m-d");
			$time1 = date("h:i:s");
			$date_temp = strtotime(date('Y-m-d'));
			$due_date = date('Y-m-d',strtotime('+15 days',$date_temp));
			
			// we should check if the book is available first. if not show message.
			$checkQue = "SELECT availability FROM book where book_ID = ?";
			if($stmt = mysqli_prepare($link, $checkQue)){
				
				mysqli_stmt_bind_param($stmt, "s", $param_bookID);
				$param_bookID = $bookID;
				
				if(mysqli_stmt_execute($stmt)){
					
					mysqli_stmt_store_result($stmt);
					
					if(mysqli_stmt_num_rows($stmt) == 1) {
						
						mysqli_stmt_bind_result($stmt, $status);
						
						if(mysqli_stmt_fetch($stmt)){
							
							if($status == 'Available') {
								//insert into REPORT table
								$insert_reportQue = "INSERT INTO report(reader_ID, book_ID, action, date, time) VALUES('".$userID."','". $bookID."','".$action."','".$date1."','".$time1."')";
								
								//update 'Available' to 'Borrowed'
								$updateQue = "UPDATE book SET availability='Borrowed' where book_ID='".$bookID."'";
								
								//insert into BORROWED table
								$insert_borrowQue = "INSERT INTO borrowed VALUES('".$userID."','".$bookID."','".$date1."','".$due_date."')";
								
								//select email_id of reader
								$email_Que = "SELECT ";
								
								//execute queries
								//inserting into BORROWED table
								if ($link->query($insert_borrowQue) === TRUE) {
									//echo "New record created successfully";
									//we do nothing here
								} else {
									echo "Error: " . $insert_borrowQue . "<br>" . $link->error;
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
								echo "<script>alert('Book issued successfully');document.location='welcome_reader.php'</script>";
								//javascript alert code here
								//header("Location: welcome_reader.php");
								//exit;
							} else if($status == 'Available_Reserved'){
								//check if this is the user who reserved the book
								$check_reserveQue = "SELECT * FROM reserved WHERE bookID='".$bookID."' AND userID='".$userID."'";
								
								$result = $link->query($check_reserveQue);
								
								if ($result->num_rows > 0) {
									// this user did reserve the book. we need to issue the book to him
									//insert into REPORT table
									$insert_reportQue = "INSERT INTO report(reader_ID, book_ID, action, date, time) VALUES('".$userID."','". $bookID."','".$action."','".$date1."','".$time1."')";
									
									//update 'Reserved' to 'Borrowed'
									$updateQue = "UPDATE book SET availability='Borrowed' where book_ID='".$bookID."'";
									
									//insert into BORROWED table
									$insert_borrowQue = "INSERT INTO borrowed VALUES('".$userID."','".$bookID."','".$date1."','".$due_date."')";
									
									//delete from RESERVE table
									$delete_reserveQue = "DELETE FROM reserved WHERE bookID='".$bookID."' AND reader_ID='".$userID."'";
									
									//select email_id of reader
									$email_Que = "SELECT ";
									
									//execute queries
									//inserting into BORROWED table
									if ($link->query($insert_borrowQue) === TRUE) {
										//echo "New record created successfully";
										//we do nothing here
									} else {
										echo "Error: " . $insert_borrowQue . "<br>" . $link->error;
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
									
									//deleting from RESERVE table 
									if ($link->query($delete_reserveQue) === TRUE) {
										//echo "New record created successfully";
										//we do nothing here
									} else {
										echo "Error: " . $delete_reserveQue . "<br>" . $link->error;
									}
									
									echo "<script>alert('Book issued successfully. You had reserved it earlier');document.location='welcome_reader.php'</script>";
									
								} else {
									//this user did not reserve the book
									echo "Book is temporarily unavailable<br/>";
									$issue_err = "Book is temporarily unavailable";								  
								}
							} else {
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
		}
	}
	
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
?>
<html>
<head>
<title>Issue</title>
</head>
<body>
<?php 
    if(!empty($login_err)){
        echo '' . $issue_err . '<br/>';
    }
?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<label for="issueID">Enter the Book ID : </label>
<input type="text" id="issueID" name="issueBookID" required/>
<br/>
<input type="submit" name="issueSubmit" value="Issue"/>
</form>
<p>
<a href="welcome_reader.php">Back</a>
</p>
</body>
</html>
