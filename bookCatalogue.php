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

    //button click code here

    $displayAllBooks_Que = "SELECT book_ID, title, author, publisher, edition, price, category, staff_id, availability FROM book";

    $result = $link->query($displayAllBooks_Que);

?>
<html>
    <head>
        <title>
            Book Catalogue
        </title>
    </head>
    <body>
        <?php 
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
                        <th> Publisher </th>
                        <th> Edition </th>
                        <th> Price </th>
                        <th> Category </th>
                        <th> Staff ID </th>
                        <th> Availability </th>
                        </tr>
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
                            <?php
                            }
                }
            }
        ?>
        <p>
            <a href="welcome_staff.php"> Back </a>
        </p>
    </body>
</html>

