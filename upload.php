<?php
 

?> 

<?php include "ProjectCommon/Header.php";
      include "ProjectCommon/DbConnection.php";
      include "ProjectCommon/Picture.php";
   
      
	 // var_dump($_SESSION);
     $userId = $_SESSION['userId'];
   // $accessibilityArray = $_SESSION['accessibilityArray'];
     if ($_SESSION['userId'] == null)
    { 
       // $_SESSION['activePage'] = "MyAlbums.php";        
        exit(header('Location: Login.php'));
    }     
	 
	//loop to get individual element from the array
     for ($i = 0; $i < count($_FILES['files']['name']); $i++) { 
		
		//var_dump($_FILES["files"][$i]);
		
		 $new_picture  =new Picture();
		 $new_picture->setDbConnection($myPdo);
		 $new_picture->setTargetFolder($target_path);
		 $new_picture->setPictureSize($_FILES["files"]["size"][$i]);
		 
		 $new_picture->setFields( $_POST['album'], basename($_FILES['files']['name'][$i]), $_POST['title'] ,$_POST['description']);
		 
		 $new_picture->savePicture($_FILES['files']['tmp_name'][$i]);
		
  
    }
	
	header('Location: MyPictures.php');
	
	
	
?>
 


