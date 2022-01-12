
<?php
session_start();  // start PHP session! 
?> 

<?php
include "ProjectCommon/Header.php";
include "ProjectCommon/DbConnection.php";
include "ProjectCommon/Picture.php";

$userId = $_SESSION['userId'];
// $accessibilityArray = $_SESSION['accessibilityArray'];
if ($_SESSION['userId'] == null) {
    // $_SESSION['activePage'] = "MyAlbums.php";        
    exit(header('Location: Login.php'));
}
$friendId = $_GET['friendId'];
?>
<?php


//$submit_name = $_SESSION['submit_name'];
//echo $submit_name;
//echo $friendId;


$sql = "SELECT Album_Id, Title FROM Album WHERE Owner_Id = :friendId AND Accessibility_Code = 'shared'";
//$sql = "SELECT Album_Id, Title FROM Album where Owner_Id = :userId";
$pStmt = $myPdo->prepare($sql);
$pStmt->execute(['friendId' => $friendId]);
$albums = $pStmt->fetchAll();
$option_list = "";

$albumId = (isset($_POST['album'])) ? $_POST['album'] : $albums[0]["Album_Id"];

$pictureId = (isset($_POST['pictureId'])) ? $_POST['pictureId'] : "";


$picture = new Picture();
$picture->setDbConnection($myPdo);
$picture->setTargetFolder($target_path);
$picture->setAlbumId($albumId);
$thumbnails = $picture->getAlbumTubnails();

if (!$pictureId && count($thumbnails) > 0) {

    $pictureId = $thumbnails[0]['Picture_Id'];
}
$mainPicture = $picture->getPicture($pictureId);
if (count($albums) > 0) {
    //$option_list .= '<option value="" disabled >Select Album </option>';	
    foreach ($albums as $row) {
        $selected = ($albumId == $row["Album_Id"]) ? "Selected" : "";
        $option_list .= '<option value="' . $row["Album_Id"] . '"  ' . $selected . ' >' . $row["Title"] . '</option>';
    }
} else {
    $option_list .= '<option value="" >No Album exists</option>';
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 text-center">
            <h1>Friend Pictures</h1>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-8 ">

            <form action="" method="post" name="myPicturesForm" >
                <input type="hidden" name="pictureId" id="pictureId" value="<?php echo $pictureId; ?>" >
                <div class='form-group row'>                
                    <div class='col-lg-12'>
                        <select name="album" id="albumId" size="1" class='form-control' >        
<?php echo $option_list; ?>
                        </select>
                    </div>                 
                </div>

            </form>           

        </div>
    </div>        


    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 text-center">
            <h1><?php echo $mainPicture['Title']; ?></h1>
        </div>
    </div>
    <div class="row">



        <div class="col-md-6"> 



            <img src="<?php echo $mainPicture['img_src']; ?>"  class="rounded" width="100%" style="max-height:500px;"> 

            <hr>
            <div class="row">

<?php
foreach ($thumbnails as $row) {

    echo '<div class="col-xs-6 col-md-2">
			<button class="thumbnail" rel="' . $row['Picture_Id'] . '" data-value ="' . $row['Picture_Id'] . '" >
			  <img src="' . $row['img_src'] . '"  alt="' . $row['FileName'] . '" style="height: 80px; width: 100%; display: block;" >
			</button>
		  </div>';
}
?> 

            </div>
        </div>

        <div class="col-md-6 ">
            <div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Description</h3>
                    </div>
                    <div class="panel-body">
<?php echo $mainPicture['Description']; ?>
                    </div>

                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Comments</h3>
                    </div>
                    <div class="panel-body">
                        Comments here ! not implemented
                    </div>

                </div>
            </div>	

            <textarea  class='form-control' id='description'  name='description' style='height:100px' placeholder="Leave Comment" ></textarea>
            <br>
            <button type='submit' name='btnsubmit' class='btn  btn-primary '>Add Comment</button>

        </div>
    </div>      
</div>

<script>

    $(document).ready(function() {
    $('button.thumbnail').click(function(event) { 
    $("#pictureId").val($(this).data("value"));
    $('form[name="myPicturesForm"]').submit(); 
    });

    $('#albumId').on('change', function(e){
    $("#pictureId").val("");
    $(this).closest('form').submit();

    });

    });

</script>

<?php include "ProjectCommon/Footer.php" ?>

