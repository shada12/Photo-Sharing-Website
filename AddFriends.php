
<?php

session_start();  // start PHP session! 
?> 

<?php include "ProjectCommon/Header.php";
      include "ProjectCommon/DbConnection.php";
   
      
     $userId = $_SESSION['userId'];
   // $accessibilityArray = $_SESSION['accessibilityArray'];
     if ($_SESSION['userId'] == null)
    { 
       // $_SESSION['activePage'] = "MyAlbums.php";        
        exit(header('Location: Login.php'));
    }     
?>

<?php
   // $userId = $_SESSION['userId'];
//Login ID from Session
$requester = $_SESSION["userId"];

//Main

$requestee = "";
$requesteeErr = "";


$requestee = $_POST['requestee'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $requestee = trim($requestee);
    
    $valid = false;
    $_SESSION["valid"] = $valid; 

        
    function ValidateRequestee() {
        
        //The entered user ID must exists.
        //One cannot send a friend request to himself/herself. 
        
        global $requestee, $requester, $requesteeErr, $myPdo;
        
	$sql = "SELECT UserId FROM User WHERE UserId = '".$requestee."'";
            $pStmt = $myPdo->prepare($sql);
            $pStmt->execute([
                'requestee' => $requestee]);

            $row = $pStmt->fetch(PDO::FETCH_ASSOC);
            if($_POST['requestee']){
                
                if($requestee == $requester)           
                {
                    $requesteeErr = "<span style='color:red;'>"."cannot use your own User ID". "</span>";
                }                 
                elseif($row)
                {
                        function ValidateRequestee2() {
        
                        //One cannot send a friend request to someone who is already his/her friend.
                        //If A sends a friend request to B, while A has a friend request from B waiting for A to accept, A and B become friends.

                        global $requestee, $requester, $requesteeErr, $myPdo;

                        //Get friends for a user. 
                        //Returns all friends to whom the user initiated the requests.
                        //Returns all friends whose requests the user accepted.		
                        $sql = "SELECT * FROM Friendship "
                                . "WHERE "
                                . "Friend_RequesteeId = '".$requestee."' "
                                . "AND "
                                . "Friend_RequesterId = '".$requester."'"
                                . "AND Status = 'accepted' "
                                
                                . "UNION "
                                
                                . "SELECT * FROM Friendship "
                                . "WHERE "
                                . "Friend_RequesterId = '".$requestee."' "
                                . "AND Status = 'accepted' "
                                . "AND "
                                . "Friend_RequesteeId = '".$requester."' "
                                . "AND Status = 'accepted'";


                            $pStmt = $myPdo->prepare($sql);
                            $pStmt->execute([
                                'requestee' => $requestee,
                                'requester' => $requester]);

                            $row = $pStmt->fetch(PDO::FETCH_ASSOC);

                                if($row)           
                                {
                                    $requesteeErr = "<span style='color:red;'>"."You are already friends"."</span>";
                                    //Do nothing
                                }                 
                                else
                                {
                                 //check if friend request pending
                                 $sqlIncomingRequest = "SELECT * FROM Friendship "
                                        . "WHERE Friend_RequesterId = '".$requestee."' "
                                        . "AND Friend_RequesteeId = '".$requester."' "
                                        . "AND Status = 'request'";

                                    $pStmt = $myPdo->prepare($sqlIncomingRequest);
                                    $pStmt->execute([
                                        'requestee' => $requestee,
                                        'requester' => $requester]);

                                    $row = $pStmt->fetch(PDO::FETCH_ASSOC);

                                    if($row)
                                    {
                                        //accept incoming friend request if pending
                                        $sql3 = "UPDATE Friendship "
                                                . "SET Status = 'accepted' "
                                                . "WHERE Friend_RequesterId = '".$requestee."'"
                                                . "AND Friend_RequesteeId = '".$requester."'";
                                        
                                    $pStmt = $myPdo->prepare($sql3);
                                    
                                    $pStmt->execute([
                                    'requestee' => $requestee,
                                    'requester' => $requester]);
                                    
                                    $requesteeErr = "<span style='color:red;'>"."Friend request accepted"."</span>";                                    
                                    }
                                    else
                                    {
                                        //Check if friend request already sent by current user    
                                        $sqlOutgoingRequest = "SELECT * FROM Friendship "
                                            . "WHERE Friend_RequesterId = '".$requester."' "
                                            . "AND Friend_RequesteeId = '".$requestee."' "
                                            . "AND Status = 'request'";

                                        $pStmt = $myPdo->prepare($sqlOutgoingRequest);
                                        $pStmt->execute([
                                            'requestee' => $requestee,
                                            'requester' => $requester]);
                                       
                                         $row = $pStmt->fetch(PDO::FETCH_ASSOC);

                                        if($row)
                                        {
                                            $requesteeErr = "<span style='color:red;'>"."You already sent this user a friend request" . "</span>";
                                        }
                                        else
                                        {
                                            //Send a friend request if no friend request sent
                                            $sql4 = "INSERT INTO Friendship VALUES( '".$requester."', '".$requestee."', 'request')";
                                            $pStmt = $myPdo->prepare($sql4);

                                            $pStmt->execute([
                                            'requestee' => $requestee,
                                            'requester' => $requester]);

                                            $sql5 = "SELECT Name FROM User WHERE UserId = '".$requestee."'";
                                            $pStmt5 = $myPdo->prepare($sql5);
                                            $pStmt5->execute([
                                                'requestee' => $requestee]);

                                            while($result = $pStmt5->fetch(PDO::FETCH_ASSOC))
                                            {                                       
                                                $requesteeErr = "<span style='color:green;'>"
                                                        . "Your friend request was sent to <strong>".$result['Name']."</strong> (ID:".$requestee.").</br>"
                                                        . "Once <strong>".$result['Name']."</strong> accepts your request you will be friends and </br>"
                                                        . "will be able to view each other's shared albums."
                                                        . "</span>"; 
                                            } 
                                        }
                                                                        
                                    }
                                    
                                    }           
                    } ValidateRequestee2();
                } 
                else
                {
                    $requesteeErr = "<span style='color:red;'>"."User ID does not exist"."</span>";
                } 
            }
            
    } ValidateRequestee();

    $_SESSION["valid"] = $valid;
                    
}

?>

<html>
    <head>
        <meta charset="UTF-8">
           <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
           <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>  
           <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
        <style>
        </style>
        <title></title>
    </head>
    
    <?php    
    //test
   // echo $requester;
    ?>
    
    <body>
        <h1>Add Friend</h1>
          <br/>
        <p>Welcome <b> <?php print($_SESSION["usrName"]);?></b>
            (not you? change user <a href="Login.php">here</a>).</br>
        </p>  
        <p>
            Enter the ID of the user you want to be friends with.
        </p>
        
    <form  id="myForm" method ="post" action="AddFriends.php">  
        
        <span id="error"><?php echo $requesteeErr;?></span></br>
        
        <tr>
            <td>ID:</td>
            <td><input type = "text" name = "requestee" value = "<?php echo ("$requestee"); ?>"/></td>
        
            <!-- create Un-Friend Button -->
            <input class="btn btn-primary" type = "submit" name = "submit" value = "Send Friend Request"/>
        </tr>
    </form>
        
    </body>
    <?php include "ProjectCommon/Footer.php" ?>

</html>







