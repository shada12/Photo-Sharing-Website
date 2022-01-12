

<?php
session_start();  // start PHP session! 
?> 
<?php include "ProjectCommon/Header.php" ?>

<?php
  
    //include 'ProjectCommon/Functions.php';
    
    $userId = $_SESSION['userId'];
    $accessibilityArray = $_SESSION['accessibilityArray'];
    
    //only authenticated users access this page. Other than that, back to loging +
    //creating a session to make user come back here after authentitcated
     if ($_SESSION['userId'] == null)
    { 
        $_SESSION['activePage'] = "MyAlbums.php";        
        exit(header('Location: Login.php'));
    }
    
    //Access DB
     $myPdo = new PDO("mysql:host=localhost;dbname=CST8257;port=3306;charset=utf8",
                "PHPSCRIPT",
                "1234");

    // if the user deletes 
    if ($_GET['action']== 'delete' && isset($_GET['id'])){
        $albumID = $_GET['id'];
        
        
     //   $dbConnection = parse_ini_file("ProjectCommon/db_connection.ini");
     //   extract($dbConnection);  

        $deletePictures = "DELETE FROM Picture WHERE Album_Id = :albumID";            
        $stmt = $myPdo->prepare($deletePictures);

        $delalbum = "DELETE FROM Album WHERE Album.Album_id = :albumID";
        $stmt1 = $myPdo->prepare($delalbum);
        $stmt->execute([albumID => $albumID]);
        $stmt1->execute([':albumID' => $albumID]);
        //$stmt1->commit;
    }            
    //Checking albums per user and number of pictures per album
    $sql = "SELECT a.Title, a.Date_Updated, ac.Description, a.Album_id, COALESCE(pictures, 0) number_pictures "
            . "FROM album a "
            . "LEFT JOIN (SELECT count(*) as pictures, Album_Id FROM Picture GROUP BY Album_Id) p ON a.Album_id = p.Album_Id "
            . "INNER JOIN accessibility ac ON ac.Accessibility_Code = a.Accessibility_Code "
            . "WHERE a.Owner_Id = :userId ORDER BY a.Title";

    $pStmt = $myPdo->prepare($sql);
    $pStmt->execute ( [':userId' => $userId] );
    $albumselUser = $pStmt->fetchAll();
    
    //Retrieving all acessibility options coming from database 
    $sql = "SELECT * FROM Accessibility ";    
    $pStmt = $myPdo->prepare($sql); 
    $pStmt->execute();
    
    //Put each record into an array
    $accessibilityArray = null;     //setting array to empty at first
    foreach ($pStmt as $row)
    {
        $accessibility = array( $row['Accessibility_Code'], $row['Description'] ); 
        $accessibilityArray[] = $accessibility;
    }
    $_SESSION['accessibilityArray'] = $accessibilityArray;      //session with all semesters from database       
    
    if(isset($_POST['btnsubmit'])){
        if(isset($_POST['selectAccess'])){
            $sql = "UPDATE Album SET Accessibility_Code = :access_code WHERE Album_id = :album_id";
            $optionsforaccess = $_POST['selectAccess'];
            // for each item in selectAcessibility array (key = album id, value = accessibility code)
            for ($i=0; $i < count($optionsforaccess); $i++) {
                $albumselUser[$i][2] = $optionsforaccess[$i];
                $pStmt = $myPdo->prepare($sql);
                $pStmt->execute(array(':access_code' => $albumselUser[$i][2], ':album_id' => $albumselUser[$i][3]));
            }
            $pStmt->commit;
            exit(header('Location: MyAlbums.php')); //refreshes page to get the current value
        }
    }
         
?>

<div class="container-fluid">
        <br>
        <h1>My Albums</h1>
        <br>
        <h4>Welcome <b> <?php print($_SESSION["usrName"]);?></b>! (Not you? Change your session <a href="Login.php">here</a>)</h4>

        <form method='post' action='MyAlbums.php'>
            <div class='col-lg-4' style='color:red'> <?php print $validatorError;?></div>
            <br><br><br>
            <div class='row'>               
                <div class='col-lg-10 col-md-9 col-sm-9 col-xs-7'></div>
                <div class='col-lg-2 col-md-3 col-sm-3 col-xs-5'>
                    <b><a href="AddAlbum.php">Create a New Album</a></b>
                </div>
            </div>  
            <table class="table">
                <!-- display table header -->
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Date Updated</th>
                        <th scope="col">Number of Pictures</th>
                        <th scope="col">Accessibility</th>
                        <th scope="col"></th>                                                                              
                    </tr>
                </thead>   

                <tbody>
                    <?php
                    foreach ($albumselUser as $var)
                    {
                        echo "<tr>";
                        echo '<td scope="col"><a href="MyPictures.php?action=album&id='.$var[3].'">'.$var[0].'</a></td>'; // Title
                        echo "<td scope='col'>".$var[1]."</td>"; // Date Updated
                        echo "<td scope='col'>". $var[4] . "</td>"; // Number of pictures
                        //displaying accessibility dropdown menu for each album
                        echo "<td scope='col'><select name='selectAccess[]' class='form-control' >  ";
                        
                        foreach ($accessibilityArray as $row)
                        {   
                            echo "<option value='$row[0]' "; //accessibility description 
                            if ($row[1] == $var[2]) //if description from dropdown equals to description from database
                                { 
                                    echo "selected='selected'"; //select this description
                                }
                            echo ">" . $row[1] . "</option>"; //display description text
                        }          
                        echo "</select>";
                        echo "<td scope='col'><a href='MyAlbums.php?action=delete&id=$var[3]' onclick='return myFuncDelete()'/a>Delete</td>"; // delete button
                        echo "</tr>";
                    }                              
                    ?>
                </tbody>
            </table>

            <br>
            <div class='row'>               
                <div class='col-lg-9 col-md-9 col-sm-8 col-xs-6'></div>
                <div class='col-lg-2 col-md-2 col-sm-3 col-xs-6'>
                    <button type='submit' name='btnsubmit' class='btn btn-block btn-primary'>Save Changes</button>  
                </div> 
            </div>  
    </form>   
    </div>
<script>
    function myFuncDelete() 
    {
        if(confirm("The selected album and its pictures will be deleted!"))
        {
            return true;
        }
        else
        {
            return false; 
        }      
    }
</script>
<?php include "ProjectCommon/Footer.php" ?>




