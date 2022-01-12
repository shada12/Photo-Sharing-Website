

<?php
session_start();  // start PHP session! 
?> 
<?php include "ProjectCommon/Header.php" ?>


<?php
// Session for title
$title = htmlspecialchars($_POST['title']);
$_SESSION['title'] = $title;
// session for Acessibility
$selectAccess = $_POST['selectAccess'];
// session for description.
$description = htmlspecialchars($_POST['description']);
$_SESSION['description'] = $description;
// userid session 
$userId = $_SESSION['userId'];
$userName;
// errors
$validatorError = "";
$titleError = "";
//only authenticated users access this page. Other than that, back to loging +
//creating a session to make user come back here after authentitcated
if ($_SESSION['userId'] == null) {
    $_SESSION['activePage'] = "AddAlbum.php";
    exit(header('Location: Login.php'));
}

//Connection to DBO            

$myPdo = new PDO("mysql:host=localhost;dbname=CST8257;port=3306;charset=utf8",
        "PHPSCRIPT",
        "1234");

//////////////////////////

$userName = $_SESSION["usrName"];

//  echo $userName;
//////////////////////////
//Retrieving all acessibility options coming from database 
$sql = "SELECT * FROM Accessibility ";
$pStmt = $myPdo->prepare($sql);
$pStmt->execute();

//Put each record into an array
foreach ($pStmt as $row) {
    $accessibility = array($row['Accessibility_Code'], $row['Description']);
    $accessibilityArray[] = $accessibility;
}
$_SESSION['accessibilityArray'] = $accessibilityArray; //session with all semesters from database       
//Submit button:
if (isset($_POST['btnsubmit'])) {
    //VALIDATORS:
    //////////////////////////////////////////////////////////////////////////


    if (empty($title)) {

        $titleError = "Please type in an album title!";
    }




    if ($accessibility == "-1") {

        $validatorError = "Please select one type of accessibility!";
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //    if (ValidateName($title) == 1) //changed from name to title
    ////    {
    //      $titleError = "Please type in an album title!";
    //    }  
    //   if ($selectAccess  == '0'){ //acessibility
    //        $validatorError = "Please select one type of accessibility!";
    //    }
    //    
    //    ///////////////////////////////////////////////////////////////////////////////////////////////
    //checking to see if the album title already exists  
    //$sql = "SELECT Album_Id, Title, Description, Date_Updated, Accessibility_Code "
    //     . "FROM Album  WHERE Owner_Id = :userId";
    $titlesql = 'SELECT * FROM Album WHERE Album.Title = :albumTitle and '
            . 'Album.Owner_Id = :userID';
    $titlestmt = $myPdo->prepare($titlesql);

    $titlestmt->execute([albumTitle => $title, userID => $userId]);

    $checkTitle = $titlestmt->fetchAll();

    if ($checkTitle != null) {
        $titleError = "Album title already exists!";
    }
    //If passing the validation:
    if ($titleError == "" && $validatorError == "") {
        $albumId = null;
        $date = date("Y/m/d");
        $access = $_POST['selectAccess'];
        // INSERT INTO `Album`(`Album_id`, `Title`, `Description`, `Date_Updated`, `Owner_Id`, `Accessibility_Code`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6])
        //creating new album
        $sql = "INSERT INTO Album (Album_id, Title, Description, Date_Updated, Owner_Id, Accessibility_Code) VALUES (:albumId, :albumTitle, :albumDescription, :albumDate, :userID, :accessibility) ";
        $pStmt = $myPdo->prepare($sql);
        $pStmt->execute(array(':albumId' => $albumId, ':albumTitle' => $title, ':albumDescription' => $description, ':albumDate' => $date, ':userID' => $userId, ':accessibility' => $access));
        $pStmt->commit;

        //view MyAlbums page(?)
        header('Location: MyAlbums.php');
        exit;
    }
}

//Clear button:
if (isset($_POST['btnclear'])) {
    $_SESSION['title'] = "";
    $_POST['description'] = "";
    $_POST['selectAcess'] = "";
}
?>

<div class="container">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 text-center">
            <h1>Create New Album</h1>
        </div>
    </div>        
    <br/>

    <h4>Welcome <b><?php print $_SESSION["usrName"]; ?></b>! (Not you? Change your session <a href="Login.php">here</a>).</h4>
    <br/>

    <form method='post' action=AddAlbum.php> 
        <div class='form-group row'>
            <div class='col-lg-2 col-md-2 col-sm-2'>
                <label for='title' class='col-form-label'><b>Title:</b> </label>
            </div>
            <div class='col-lg-4'>
                <input type='text' class='form-control' value='<?php print $_SESSION['title']; ?>' id='title' name='title' >
            </div>
            <div class='col-lg-4' style='color:red'> <?php print $titleError; ?></div>
        </div>        

        <div class='form-group row'>
            <div class='col-lg-2 col-md-2 col-sm-2'>
                <label for='accessibility' class='col-form-label'><b>Accessibility:</b></label>
            </div>
            <div class='col-lg-4'>                
                <select name='selectAccess' class='form-control' >       
                    <option value='0'></option>;  
                    <!--printing the accessibility options coming from database -->
<?php
$accessibilityArray = $_SESSION['accessibilityArray'];
foreach ($accessibilityArray as $row) {
    echo "<option value='$row[0]' "; //atributing the value Ex: 18F
    if ($row[0] == $_POST['selectAccess']) { //if term coming from db is equal the one selected from user, set it as 'selected'
        echo "selected='selected'";
    }
    echo ">" . $row[1] . "</option>";
}
?>         
                </select>  
            </div>  
            <div class='col-lg-3' style='color:red'> <?php print $validatorError; ?></div>
        </div>

        <div class='form-group row'>
            <div class='col-lg-2 col-md-2 col-sm-2'>
                <label for='description' class='col-form-label'><b>Description:</b> </label>
            </div>
            <div class='col-lg-4'> 
                <textarea  class='form-control' id='description'  name='description' style='height:150px'><?php
                    if (isset($_POST['description'])) {
                        echo $_POST['description'];
                    }
?></textarea>
            </div>
        </div>
        <br/>

        <div class='row'>
            <div class="col-lg-2 col-md-0 col-sm-0"></div>
            <div class='col-lg-2 col-md-2 col-sm-2 text-left'>
                <button type='submit' name='btnsubmit' class='btn btn-block btn-primary col-lg-2'>Submit</button>
            </div>
            <div class='col-lg-2 col-md-2 col-sm-2 text-left'>
                <button type='submit' name='btnclear' class='btn btn-block btn-primary col-lg-3'>Clear</button>
            </div>
        </div> 
    </form>
</div>
<?php
include 'ProjectCommon/Footer.php';
?>
