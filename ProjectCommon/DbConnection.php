<?php
//session_start();
 
 if(!isset($_SESSION)) 
    { 
        session_start(); 
    }
?> 

<?php

// Create PDO connection that can be used in all pages 
// so there is no need to restablish connection for every function.



$sqlServer = "localhost";
$sqlUser = "PHPSCRIPT";
$sqlPassword = "1234";
$sqlDatabase = "CST8257";

//$sqlServer = "localhost";
//$sqlUser = "root";
//$sqlPassword = "";
//$sqlDatabase = "CST8257";


$target_path = "./uploads/"; //Declaring Path for uploaded images
 
try {
	
    $myPdo = new PDO("mysql:host=localhost;dbname=CST8257;port=3306;charset=utf8",
                "PHPSCRIPT",
                "1234"); 

 
				/*$myPdo = new PDO("mysql:host=localhost;dbname=cst8257;port=3306;charset=utf8",
                "root",
                "");*/
    // set the PDO error mode to exception
    $myPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   // echo "Connected successfully";
    }
catch(PDOException $err)
    {
			//echo "Connection failed: " . $err->getMessage();
    }

?>