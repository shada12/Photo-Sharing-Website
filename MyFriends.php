
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
    $userId = $_SESSION["userId"];
    
    $UnFriend = $_POST['UnFriend'];
    $checkbox1 = $_POST['checkbox1'];
    
    $accept = $_POST['accept'];
    $deny = $_POST['deny'];
    $checkbox2 = $_POST['checkbox2'];


    
    // Dec 1 2019 - I think my script is either too slow or infinite so I am getting a time out error???
    // I tried set_time_limit(0); Didn't help much.
    // I tried restarting apache and sql then it was fine
    
if((isset($UnFriend))&&($checkbox1 != null)) {
            
            $count = count($checkbox1);
            //echo "  Count: ".$count;
            //print_r($checkbox1);
   
                    $checked = join("','", $checkbox1);
                    $sql2 = "DELETE FROM Friendship "
                        . " WHERE ((Friend_RequesterId = :userId AND Friend_RequesteeId IN ('$checked')) "
                        . " OR (Friend_RequesterId IN ('$checked') AND Friend_RequesteeId = :userId)) "
                        . " AND Status='accepted'";
                    
                    
                    $pStmt = $myPdo->prepare($sql2);
                    $pStmt->execute(['checkbox1' => $checkbox1,
                                     'userId' => $userId]);  

                      unset($UnFriend);
                      //exit(header('Location: MyFriends.php'));
                      //header("Location: MyFriends.php");
            }
        if((isset($accept))&&($checkbox2 != null)) {
            $count = count($checkbox2);
            
            $checked = join("','", $checkbox2);
            
            echo "...";
            print_r($checked);

            $sqlAccept = "UPDATE Friendship 
                SET Status = 'accepted' 
                WHERE Friend_RequesterId IN ('$checked') 
                AND Friend_RequesteeId = '".$userId."' AND Status = 'request'";

            
            $pStmt = $myPdo->prepare($sqlAccept);
            $pStmt->execute(['checkbox2' => $checkbox2,
                             'userId' => $userId]);  

            unset($accept);
            //unset($deny);
            //exit(header('Location: MyFriends.php'));
            //header("Location: MyFriends.php");

        }

        if((isset($deny))&&($checkbox2 != null)){
            
            $count = count($checkbox2);
   
            $checked = join("','", $checkbox2);
            
            $sqlDeny = "DELETE FROM Friendship 
                WHERE Friend_RequesterId IN ('$checked') 
                AND Friend_RequesteeId = '".$userId."' AND Status = 'request'";

            $pStmt = $myPdo->prepare($sqlDeny);
            $pStmt->execute(['checkbox2' => $checkbox2,
                             'userId' => $userId]);  

            //unset($accept);
            unset($deny);
            //exit(header('Location: MyFriends.php'));
            //header("Location: MyFriends.php");
                
        }
            
    function show_friends() 
     {  
        global $myPdo, $userId, $checkbox1, $UnFriend;
                
        //Dec 1 2019 - The query works but does not display users who have 0 shared albums         
            $sql= "SELECT Friend_RequesteeId, COUNT(Owner_Id) as count, Name
            FROM Album 
                INNER JOIN Friendship ON
            Album.Owner_Id = Friendship.Friend_RequesteeId
                INNER JOIN User ON
                Album.Owner_Id = User.UserId
            WHERE 
            Friend_RequesterId = :userId
            AND Friend_RequesteeId != :userId
            AND Status = 'accepted'
            AND Album.Accessibility_Code = 'shared' 
                UNION
            SELECT Friend_RequesterId, COUNT(Owner_Id) as count, Name
            FROM Album 
                INNER JOIN Friendship ON
            Album.Owner_Id = Friendship.Friend_RequesterId
                 INNER JOIN User ON
            Album.Owner_Id = User.UserId
            WHERE
            Friendship.Status = 'accepted'
            AND Album.Accessibility_Code = 'shared' 
            AND Friend_RequesteeId = :userId 
            AND Friend_RequesterId != :userId
            GROUP BY Owner_Id";
   
        $pStmt = $myPdo->prepare($sql);
        $pStmt->execute(['userId' => $userId]);
        

           while(($row = $pStmt->fetch(PDO::FETCH_ASSOC)))
          {  
               
               echo '<tr>';
               echo "<td scope='col'><a href='FriendPictures.php?friendId=".trim($row['Friend_RequesteeId'])."'>".$row["Name"]."</a></td>";
               //echo '<td scope="col"><a href="FriendPictures.php">'.$row["Friend_RequesteeId"].'</a></td>';
               echo '<td scope="col" value="'.$row["count"].'">'.$row["count"].'</td>';                
     /*sneaky*/echo "<td style='color:#FFF;'><input name='checkbox1[]' type='checkbox' value='".$row['Friend_RequesteeId']."'>".$row['Friend_RequesteeId']."</td>\n\n";
               echo '</tr>';    
               
          }                     
                 

     }  
     
        
     
function show_friendRequests() 
     {  
        global $myPdo, $userId, $checkbox2;
          
        
                $sql = "SELECT Friend_RequesterId, Friend_RequesteeId, Status, UserId, Name
                    FROM Friendship 
                        INNER JOIN User ON
                    Friendship.Friend_RequesterId = User.UserId
                    WHERE 
                    Friend_RequesterId != :userId
                    AND Friend_RequesteeId = :userId
                    AND Status = 'request'";

        $pStmt = $myPdo->prepare($sql);
        $pStmt->execute(['userId' => $userId]);

          while($row = $pStmt->fetch(PDO::FETCH_ASSOC))
          {  
               echo '<tr>';
               echo '<td scope="col" value="'.$row["Name"].'">'.$row["Name"].'</td>';
               //echo '<td scope="col" value="'.$row["Friend_RequesterId"].'">'.$row["Friend_RequesterId"].'</td>';
     /*sneaky*/echo "<td style='color:#FFF;'><input name='checkbox2[]' type='checkbox' value='".$row['Friend_RequesterId']."'>".$row['Friend_RequesterId']."</td>\n\n";
               echo '</tr>';
          }  
    
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
    
    <body>
        <h1>My Friends</h1>
        <br/>
        <p>Welcome <b> <?php print($_SESSION["usrName"]);?></b> (not you? change user <a href="Login.php">here</a>).</br>
        </p>  
        
    <div  align="right"> <p ><a href="AddFriends.php">Add Friends</a></p> <p><a href="FriendPictures.php">Friends Pictures</a></p>
    </div>

    <form action="MyFriends.php" method="post">    
    <table class="table" >
            <thead>
                <tr>
                    <td scope="col">Name</td>
                    <td scope="col">Shared Albums</td>
                    <td scope="col">Un-Friend</td>
                </tr>
            </thead>
            
            <div class="row" id="show_friends">
                <?php show_friends();?>
            </div>           
        </table>
        
        <!-- create Un-Friend Button -->
        <div align="right">
        <input type = "submit" value = "Defriend Selected" name="UnFriend" onclick=" return confirm('The selected friends will be un-friended!')" class="btn btn-primary"/>
        </div>
        </br></br>
        
        <tr><td>Friend Requests:</td><td>
        <table class="table">
            <thead>
                <tr>
                    <td scope="col">Name</td>
                    <td scope="col">Accept or Deny</td>
                </tr>
            </thead>
            
            <div class="row" id="show_friendRequests">
                <?php show_friendRequests();?>
            </div>
            
        </table>
        
        <!-- create Accept/Deny buttons -->
        <div align="right">
        <input type = "submit" value = "Accept Selected" name = "accept" class="btn btn-primary"/>
        <input type = "submit" value = "Deny Selected" name = "deny" onclick=" return confirm('Are you sure you want to deny this friend's request!')" class="btn btn-primary"/>
        </div>
        </br></br>
        </form>
    </body>
    <?php include "ProjectCommon/Footer.php" ?>

</html>







