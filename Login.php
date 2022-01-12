
<?php
session_start();  // start PHP session! 
?> 
<?php include "ProjectCommon/Header.php" ?>

<?php

class User {

    private $userId;
    private $name;
    private $phoneNumber;
    private $passWord;

    function __construct($userId, $name, $phoneNumber, $passWord) {
        $this->userId = $userId;
        $this->name = $name;
        $this->phoneNumber = $phoneNumber;
        $this->passWord = $passWord;
    }

}

$userId = "";
$passWord = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
$userId = $_POST["userId"];
$passWord = $_POST["passWord"];
$_SESSION["userId"] = $userId;

    $userId = trim($userId);
    $passWord = trim($passWord);


    $valid = false;
    $_SESSION["valid"] = $valid;
    $userIdError = $passWordError = "";
    
    function ValidateUserId($userId) {

        if (empty($userId)) {

            return "User ID field can not be blank";
        } 
    }

    function ValidatePassword($passWord) {
        if (empty($passWord)) {

            return "Password field can not be blank";
        }
    }

    $userIdError = ValidateUserId($userId);
    $passWordError = ValidatePassword($passWord);

    $_SESSION["valid"] = $valid;


    if ($userIdError == "" && $passWordError == "") {


        $valid = true;
        $_SESSION["valid"] = $valid;

        
        $hashedPassword  = sha1($passWord);
        $myPdo = new PDO("mysql:host=localhost;dbname=CST8257;port=3306;charset=utf8",
                "PHPSCRIPT",
                "1234");
        
        
        ///////////////
        //user name
        $sqlUser = "SELECT * From User where UserId = :userId";
$pSt = $myPdo->prepare($sqlUser);
$pSt->execute(['userId' => $userId]);

$rows = $pSt->fetch(PDO::FETCH_ASSOC);

$userName = $rows['Name'];
        
        $_SESSION["usrName"] = $userName;
        
        ////////////////

        function getUserById($userId, $hashedPassword, $myPdo) {


            $sql = "SELECT UserId , Password FROM User WHERE UserId = :userId && Password = :passWord";

            $pStmt = $myPdo->prepare($sql);
            $pStmt->execute(['userId' => $userId,
                'passWord' => $hashedPassword]);

            $row = $pStmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return new User($row['UserId'], $row['Name'], $row['Phone'], $row['Password']);
            } else {
                return null;
            }
        }

        if (getUserById($userId, $hashedPassword, $myPdo) != null) {


            header("Location: http://localhost:8080/CST8257Project/AddAlbum.php");
        } else {

            $userIdError = "No match for user name and/or password";
        }
    }
}//post
?>


 

<div class="container-fluid">
    <form  id="myForm" method ="post" action="Login.php">

          <h1>Log In</h1>
    <p>You need to <a href="NewUser.php" style='color:blue'>sign up </a>if you are a new user</p>
        
        <form method='post' action=Login.php>            
            <div class='row'>
                <div class='col-lg- col-md-4 col-sm-4' style='color:red'> <?php print $validateError;?></div>
            </div>
            <br>
            <div class='form-group row'>
                <div class="col-lg-1 col-md-1 col-sm-2">
                    <label for='userId' class='col-form-label'><b>User ID:</b> </label>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4">
                    <input type='text' class='form-control' id='userID'  value='<?php print("$userId") ?>' name='userId'>
                </div>
                <div class='col-lg-4 col-md-2 col-sm-4' style='color:red'> <?php print($userIdError); ?></div>
            </div>
            <br/>

            <div class='form-group row'>
                <div class="col-lg-1 col-md-1 col-sm-2">
                    <label for='passWord' class='col-form-label'><b>Password:</b> </label>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4">
                    <input type='password' class='form-control' id='password'  value='<?php print($passWord) ?>' name='passWord' ></div>
                <div class='col-lg-4 col-md-2 col-sm-4' style='color:red'> <?php print($passWordError) ?></div>
            </div><br>
           <!--- Submit btn ---> 
              
         <div class='row'>
                <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">&nbsp;</div>
                <div class='col-lg-1 col-md-1 col-sm-2 col-xs-2 text-left'>
                    <button type='submit' name='btnsubmit' class='btn btn-block btn-primary'>Submit</button>
                </div>
                <div class='col-lg-1 col-md-1 col-sm-2 col-xs-2 text-left'>
                    <button type='submit' name='btnclear' onclick= "clearform()" class='btn btn-block btn-primary'>Clear</button>
                </div>
            </div>   
        </form>
    </div>

<script>
    function clearform()
    {

    document.getElementById("userID").value = "";   
    document.getElementById("password").value = "";


    }
</script>

<?php include "ProjectCommon/Footer.php" ?>




