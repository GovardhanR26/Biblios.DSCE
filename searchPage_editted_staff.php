<?php
session_start();

date_default_timezone_set("Asia/Calcutta");

	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin_staff"]) || $_SESSION["loggedin_staff"] !== true) {
		header("location: login_staff.php");
		exit;
	}
	
	require 'connect_db.php';
	
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
							</tr>
						<?php
						}
						?>
							</table>
						<?php
					} else {
						echo "<br/>No results found<br/>";
					}
				} else {
					echo "<br/>No results found<br/>";
				}					
			} else {
				echo "Please enter text";
			}				
		} else {
				//echo "Got false bro";
		}		
	?>
	<a href="welcome_staff.php">Back</a>
</div>
</body>
</html>