<?php
session_start();  // start PHP session! 
?>

<?php include "ProjectCommon/Header.php" ?>


<?php

class User {
    
    private $UserId;
    private $name;
    private $phoneNumber;
    private $passWord;
    
    function __construct($UserId, $name, $phoneNumber, $passWord) {
        $this->UserId = $UserId;
        $this->name = $name;
        $this->phoneNumber = $phoneNumber;
        $this->passWord = $passWord;
    }
    function getUserId() {
        return $this->UserId;
    }

    function getName() {
        return $this->name;
    }

    function getPhoneNumber() {
        return $this->phoneNumber;
    }

    function getPassWord() {
        return $this->passWord;
    }

    function setUserId($UserId) {
        $this->UserId = $UserId;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }

    function setPassWord($passWord) {
        $this->passWord = $passWord;
    }      
}

//$hashedPassword = sha1($_POST[‘txtPassword’]);	

$userId = $_POST["userId"];
$name = $_POST["name"];
$phoneNumber = $_POST["phoneNumber"];
$passWord = $_POST["passWord"];
$passWordAgain = $_POST["passWordAgain"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userId = trim($userId);
    $name = trim($name);
    $phoneNumber = trim($phoneNumber);
    $passWord = trim($passWord);
    $passWordAgain = trim($passWordAgain);

    $valid = false;


    $userIdError = $nameError = $phoneNumberError = $passWordError = $passWordAgainError = "";

    function ValidateUserId($userId) {

        if (empty($userId)) {

            return "User ID field can not be blank";
        } else {

            return "";
        }
    }

    function ValidateName($name) {
        if (empty($name)) {

            return "Name field can not be blank";
        } else {
            return "";
        }
    }

    function ValidatePhone($phone) {

        if (empty($phone)) {

            return "Phone number field can not be blank";
        } else {

            $phoneNumberRegex = "/^[2-9][0-9]{2}\-[2-9][0-9]{2}\-[0-9]{4}$/";
            if (!preg_match($phoneNumberRegex, $phone)) {

                return "Incorrect Phone Number";
            }
        }
    }

    function ValidatePassword($passWord) {
        if (empty($passWord)) {

            return "Password field can not be blank";
        } else {

            $passwordRegex = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/";

            if (!preg_match($passwordRegex, $passWord)) {

                return "Password is at least 6 characters of at least one number, and one uppercase and lowercase letter ";
            }
        }
    }

    function ValidatePasswordAgain($passWordAgain, $passWord) {

        if (empty($passWordAgain)) {

            return "Password Again field can not be blank";
        }


        if (strcmp($passWordAgain, $passWord) != 0) {


            return "Incompatible Password";
        }
    }

    $userIdError = ValidateUserId($userId);
    $nameError = ValidateName($name);
    $phoneNumberError = ValidatePhone($phoneNumber);
    $passWordError = ValidatePassword($passWord);
    $passWordAgainError = ValidatePasswordAgain($passWordAgain, $passWord);
    
  
    $_SESSION["userId"] = $_POST["userId"];
    $_SESSION["name"] = $_POST["name"];
    $_SESSION["phoneNumber"] = $_POST["phoneNumber"];
    $_SESSION["passWord"] = $_POST["passWord"];
  
    
    if ($nameError == "" && $phoneNumberError == "" && $userIdError == "" && $passWordError == "" && $passWordAgainError == "") {

        
        $isValid = true;
        $_SESSION["isValid"] = $isValid;
        
        $hashedPassword  = sha1($_POST["passWord"]);
        //$hashedPassword = sha1($_ GET[‘txtPassword’]);	

        $myPdo = new PDO("mysql:host=localhost;dbname=CST8257;port=3306;charset=utf8",
                "PHPSCRIPT",
                "1234");

        function getUserById($userId, $myPdo) {


            $sql = "SELECT UserId , Name FROM User WHERE UserId = :userId";

            $pStmt = $myPdo->prepare($sql);
            $pStmt->execute(['userId' => $userId]);

            $row = $pStmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return new User($row['UserId'], $row['Name'], $row['Phone'], $row['Password']);
            } else {
                return null;
            }
        }

        if (getUserById($userId, $myPdo) == null) {


            $sql = "INSERT INTO User  (UserId, Name, Phone,Password ) VALUES( :userId, :name, :phoneNumber, :passWord)";

            $pStmt = $myPdo->prepare($sql);

            $pStmt->execute(['userId' => $userId,
                'name' => $name,
                'phoneNumber' => $phoneNumber,
                'passWord' => $hashedPassword]);

            $error = $pStmt->errorInfo();

            header("Location: http://localhost:8080/CST8257Project/Login.php");
        } else {

            $userIdError = "The user with this ID has already signed up";
        }
    }
}

?>

    
    <div class="container-fluid">
   <form  id="myForm" method ="post" action="NewUser.php">

         <h1>Sign Up</h1>

    <p>All fields are required</p> 

        <form method='post' action=Login.php>            
            <div class='row'>
                <div class='col-lg- col-md-4 col-sm-4' style='color:red'> <?php print $validateError;?></div>
            </div>
            <br>
            <div class='form-group row'>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <label for='userId' class='col-form-label'><b>User ID:</b> </label>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4">
                    <input type='text' class='form-control' id='userID'  value='<?php print("$userId") ?>' name='userId'>
                </div>
                <div class='col-lg-4 col-md-2 col-sm-4' style='color:red'> <?php print($userIdError); ?></div>
            </div>
            <br/>
 <div class='form-group row'>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <label for='name' class='col-form-label'><b>Name:</b> </label>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4">
                    <input type='text' class='form-control' id='name'  value='<?php print($name) ?>' name='name' ></div>
                <div class='col-lg-4 col-md-2 col-sm-4' style='color:red'> <?php print($nameError) ?></div>
            </div><br>
            
            
            <div class='form-group row'>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <label for='phoneNumber' class='col-form-label'><b>Phone Number:<br/>(nnn-nnn-nnnn)</b> </label>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4">
                    <input type='text' class='form-control' id='phone'  value='<?php print($phoneNumber) ?>' name='phoneNumber' ></div>
                <div class='col-lg-4 col-md-2 col-sm-4' style='color:red'> <?php print($phoneNumberError) ?></div>
            </div><br>
            
             <div class='form-group row'>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <label for='passWord' class='col-form-label'><b>Password:</b> </label>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4">
                    <input type='password' class='form-control' id='passwordagain'  value='<?php print($passWord) ?>' name='passWord' ></div>
                <div class='col-lg-4 col-md-2 col-sm-4' style='color:red'> <?php print($passWordError) ?></div>
            </div><br>
            
            <div class='form-group row'>
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <label for='passWord' class='col-form-label'><b>Password Again:</b> </label>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4">
                    <input type='password' class='form-control' id='passWord'  value='<?php print($passWordAgain) ?>' name='passWordAgain' ></div>
                <div class='col-lg-4 col-md-2 col-sm-4' style='color:red'> <?php print($passWordAgainError) ?></div>
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

</form>

<script>
    function clearform()
    {

    document.getElementById("userID").value = "";
    document.getElementById("name").value = "";  
    document.getElementById("phone").value = "";    
    document.getElementById("password").value = "";
    document.getElementById("passwordagain").value = "";
    document.getElementsByClassName("error").value = "";

    }
</script>
<?php include "ProjectCommon/Footer.php" ?>




