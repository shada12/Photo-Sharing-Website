
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
   
 
 $sql = "SELECT Album_Id, Title FROM Album where Owner_Id = :userId";
 
 
   $pStmt = $myPdo->prepare($sql);
    $pStmt->execute ( [':userId' => $userId] );
    $albums = $pStmt->fetchAll();
	
 

$option_list =""; 

//var_dump($result->num_rows );
if ( count($albums) > 0) {
	
	$option_list .= '<option value="" disabled >Select Album </option>';
	
    // output data of each row
    
	  foreach ($albums as $row)
	{
        	
		$option_list .= '<option value="'.$row["Album_Id"].'" >'.$row["Title"].'</option>';
    }
} else {
   $option_list .= '<option value="" >No Album exists</option>';
}


?>
 

 <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 text-center">
                <h1>Upload Pictures</h1>
            </div>
        </div>        
        <br/>

        <p>Accepted picture types : JPG(JPEG), GIF and PNG.</p>
<p>You can upload multiple pictures at a time by pressing the shift key while selecting pictures.</p>
 <p>When uploading multiple pictures at a time, the specified title and description apply to all the pictures uploaded.</p>
<br/>
     
	  
      <form action="upload.php" method="post" enctype="multipart/form-data">
            <div class='form-group row'>
                <div class='col-lg-2 col-md-2 col-sm-2'>
                    <label for='title' class='col-form-label'><b>Upload to Album::</b> </label>
                </div>
                <div class='col-lg-4'>
                    					
					<select name="album" size="1" class='form-control'>        
						<?php echo $option_list;?>
					</select>


                </div>
                <div class='col-lg-4' style='color:red'>  </div>
            </div>        
        
            <div class='form-group row'>
                <div class='col-lg-2 col-md-2 col-sm-2'>
                    <label for='accessibility' class='col-form-label'><b>Files To Upload:</b></label>
                </div>
                <div class='col-lg-4'>                
							<input class='form-control' type="file" name="files[]"  multiple="multiple" />
                </div>  
                <div class='col-lg-3' style='color:red'>  </div>
            </div>
			
			
            <div class='form-group row'>
                <div class='col-lg-2 col-md-2 col-sm-2'>
                    <label for='accessibility' class='col-form-label'><b>Title:</b></label>
                </div>
                <div class='col-lg-4'>                							
							<input type='text' class='form-control' value='' id='title' name='title' >
                </div>  
                 <div class='col-lg-4' style='color:red'></div>
            </div>
            
            <div class='form-group row'>
                <div class='col-lg-2 col-md-2 col-sm-2'>
                    <label for='description' class='col-form-label'><b>Description:</b> </label>
                </div>
                <div class='col-lg-4'> 
                    <textarea  class='form-control' id='description'  name='description' style='height:150px'><?php 
                        if(isset($_POST['description']) ){
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
                    <button type='reset' name='btnclear' class='btn btn-block btn-primary col-lg-3'>Clear</button>
                </div>
            </div> 
        </form>
        </div>
		
 

 

<?php include "ProjectCommon/Footer.php" ?>




