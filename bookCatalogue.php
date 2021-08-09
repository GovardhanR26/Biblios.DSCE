<?php
    // Initialize the session
    session_start();
    date_default_timezone_set("Asia/Calcutta"); 

    // Check if the user is logged in, if not then redirect him to login page
    if(!isset($_SESSION["loggedin_staff"]) || $_SESSION["loggedin_staff"] !== true){
        header("location: login_staff.php");
        exit;
    }

    require 'connect_db.php';
    $userID = $_SESSION["id"];
    $_SESSION["mod_bookID"] = "";

    //button click code here
    if(($_SERVER["REQUEST_METHOD"]=="POST") && isset($_POST["modify_button"])) {
        $_SESSION["mod_bookID"] = $_POST["modify_button"];
        header("location: modify_book.php");
        exit;
    }

    $displayAllBooks_Que = "SELECT book_ID, title, author, publisher, edition, price, category, staff_id, availability FROM book";

    $result = $link->query($displayAllBooks_Que);

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
		.page-content th{
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
					<a href="welcome_staff.php" class="nav-link active bg-dark text-light"><i class="fa fa-th-large mr-3 text-primary fa-fw"></i>home</a>
				</li>
				<li class="nav-item">
					<a href="staffsearch.php" class="nav-link bg-dark text-light"><i class="fas fa-search mr-3 text-primary fa-fw"></i>search book</a>
				</li>
				<li class="nav-item">
					<a href="newbook.php" class="nav-link bg-dark text-light"><i class="fas fa-plus-square mr-3 text-primary fa-fw"></i>add book</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link bg-dark text-dark" id="highlight"><i class="fas fa-book-reader mr-3 text-primary fa-fw"></i>Book Catalogue</a>
				</li>
				<li class="nav-item">
					<a href="staffborrow.php" class="nav-link bg-dark text-light"><i class="fas fa-book-open mr-3 text-primary fa-fw"></i>issue book</a>
				</li>
				<li class="nav-item">
					<a href="outstandingpage.php" class="nav-link bg-dark text-light"><i class="fas fa-book mr-3 text-primary fa-fw"></i>outstanding books</a>
				</li>
				<li class="nav-item">
					<a href="newUserReg.php" class="nav-link bg-dark text-light"><i class="fas fa-user-plus mr-3 text-primary fa-fw"></i>register new user</a>
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
			<h1>Book Catalogue</h1><br/>
			<?php 
            if($result == True) {
                $row_num = mysqli_num_rows($result);
                
                if($row_num>0) {
                    //display in html table
                    ?>
                    <table class="table table-striped table-bordered" style="overflow-y:auto; height:500px; width:1100px; display:block; position:absolute;">
                        <thead class="thead-dark">
						<tr>
                        <th> Book ID </th>
                        <th> Title </th>
                        <th> Author </th>
                        <th> Publisher </th>
                        <th> Edition </th>
                        <th> Price </th>
                        <th> Category </th>
                        <th> Staff ID </th>
                        <th colspan="2"> Availability </th>
                        </tr></thead><tbody>
                        <?php
                            while($row = $result->fetch_assoc())
                            {
                            ?>	<tr>
                                <td><?php echo $row['book_ID']?></td>
                                <td><?php echo $row['title']?></td>
                                <td><?php echo $row['author']?></td>
                                <td><?php echo $row['publisher']?></td>
                                <td><?php echo $row['edition']?></td>	
                                <td><?php echo $row['price']?></td>	
                                <td><?php echo $row['category']?></td>
                                <td><?php echo $row['staff_id']?></td>
                                <td><?php echo $row['availability']?></td>
                                <!-- admin buttons below -->
                                <td><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                                <button type="submit" class="btn btn-warning" value="<?php echo $row['book_ID']?>" name="modify_button"> Modify </button>
                                </form>
                                </td>
                            <?php
                            }
							?>
							</tbody></table>
				<?php
                }
            }
        ?>
		</div>
</body>
</html>