<?php


class Picture {
    
    private $pictureId;
    private $albumId;
    private $fileName;
    private $title;
    private $description;
    private $dateAdded;
    private $UserId;
    private $targetFolder;
    private $filePath;      //Picture path  it will be generated with Picture record from DB
    private $dbConnection; // Database connection
	
	public $pictureSize=0;
	public $maxFileSize=5242880;
	public $validFileExtensions = array("jpeg", "jpg", "png","gif","JPEG","JPG","GIF","PNG", ); 
	
	
    
    function __construct( ) {
        
    }
	/*
	 function __construct($picture_id, $album_id, $filename='', $title='',$description='',$date_added='' ) {
        $this->pictureId = $picture_id;
        $this->albumId = $album_id;
        $this->fileName = $filename;
        $this->title = $title;
        $this->description = $description;
        $this->dateAdded = ($date_added)?$date_added:date("Y-m-d");
    }*/
    function getPictureId() {
        return $this->pictureId;
    }

    function getFileName() {
        return $this->fileName;
    }

    function getFileTitle() {
        return $this->title;
    } 
	function getFilePath() {
        return $this->filePath;
    } 

    function setUserId($UserId) {
        $this->UserId = $UserId;
    }  
	
	 function setPictureId($picture_id) {
       $this->pictureId = $picture_id;
     } 
	
	function setAlbumId($album_id) {
       $this->albumId = $album_id;
     }	 
	
	function setFields($album_id, $filename='', $title='',$description='',$date_added='' ) {        
        $this->albumId = $album_id;
        $this->fileName = $filename;
        $this->title = $title;
        $this->description = $description;
        $this->dateAdded = ($date_added)?$date_added:date("Y-m-d");
    }
	
	function addToDatabase(){
		
			$sql = "INSERT INTO Picture (Album_Id, FileName ,Title, Description, Date_Added) VALUES (:albumId, :fileName, :fileTitle, :fileDescription, :fileDateAdded) ";
            $pStmt = $this->dbConnection->prepare($sql); 
            $pStmt->execute(array(':albumId' => $this->albumId , ':fileName' => $this->fileName, ':fileTitle' => $this->title, ':fileDescription' => $this->description, ':fileDateAdded' => $this->dateAdded  ));            
           // $pStmt->commit;			
			$this->pictureId =$this->dbConnection->lastInsertId();
 
			$this->setNewFilePath();
		
	}
	function deleteFromDatabase(){
		
			 $delpicture = "DELETE FROM Picture WHERE Picture.Picture_Id = :pictureID";
			 $stmt1 = $this->dbConnection->prepare($delpicture);
             $stmt1->execute([':pictureID' => $this->pictureId]); 
		
	}
	function getPicture($picture_id=''){
		
		if($picture_id){
			$this->setPictureId($picture_id);
			
			 $sql = "SELECT Picture_Id , FileName , Title , Description , Date_Added "
            . "FROM Picture "            
            . "WHERE Album_Id = :albumID and Picture_Id = :pictureID ";

				$pStmt = $this->dbConnection->prepare($sql);
				$pStmt->execute ( [':albumID' => $this->albumId,':pictureID' => $this->pictureId ] );
				 
				$picture = $pStmt->fetchAll();
				if(count($picture)){
					
					$picture = $picture[0];
					 
				    $picture["img_src"] =$this->fileLocation($picture['FileName'],$picture['Picture_Id']);
				}
				 
				
				return $picture;
			
		}else return array();
		
	}
	function getAlbumTubnails(){
		
		 $sql = "SELECT Picture_Id,FileName  "
            . "FROM Picture "            
            . "WHERE Album_Id = :albumID  ORDER BY Date_Added asc";

		$pStmt = $this->dbConnection->prepare($sql);
		$pStmt->execute ( [':albumID' => $this->albumId] );
		
		//return $pStmt->fetchAll();
		
		$pictures = $pStmt->fetchAll();
		//$result=array();
		   foreach ($pictures as &$row)
			{
					$row["img_src"] =$this->fileLocation($row['FileName'],$row['Picture_Id']);
				 
			}
		return $pictures;
		
		
		
		
	}
	function copyToFolder($tmpFile){
			
			
			
			  $ext = explode('.', $this->fileName ); 
			//store extensions in the variable
			$file_extension = end($ext); 
		
		
			
		if (($this->pictureSize < $this->maxFileSize)&& in_array($file_extension, $this->validFileExtensions)) {
			//var_dump($tmpFile);var_dump($this->filePath);
				if (move_uploaded_file($tmpFile, $this->filePath)) {//if file moved to uploads folder
						//echo "All is good";
					} else {//if file was not moved.
					   //Picture record could be deleted from Database because file upload is not successfull!
					   
					   $this->deleteFromDatabase();
					    echo  ' Error!  File could not be uploaded !';
					}
					
			 }else{
				 //if file size and file type was incorrect.
				 echo "file size and file type was incorrect.";
				  //Picture record could be deleted from Database because file upload is not successfull!
					   $this->deleteFromDatabase();
				 
			 }		
		
	}
	
	
	function savePicture($tmpFile){ 
			$this->addToDatabase();
			$this->copyToFolder($tmpFile); 
	}
	
	/*
	function setNewFilePath() {
		
		$fname= explode('.',$this->fileName);
		  $fname[count($fname) - 2] =  $fname[count($fname) - 2]."_".$this->pictureId; 
		  $formatted_name = implode($fname,".");
		  
        $this->filePath =  $this->targetFolder.$formatted_name;
    } 
	*/
	function setNewFilePath() {  
        $this->filePath =  $this->fileLocation($this->fileName,$this->pictureId);
    } 
	
	
	private function fileLocation($fileName,$fileId){
		
		  $fname= explode('.',$fileName);
		  $fname[count($fname) - 2] =  $fname[count($fname) - 2]."_".$fileId; 
		  $formatted_name = implode($fname,".");
		  
        return  $this->targetFolder.$formatted_name;
		
		
	}
	
	function setDbConnection($conn) {
        $this->dbConnection = $conn;
    } 
	
	function setPictureSize($size) {
        $this->pictureSize = $size;
    } 
	function setTargetFolder($destinationPath) {
			if (!file_exists($destinationPath))
			{
				mkdir($destinationPath);
			}
	
        $this->targetFolder = $destinationPath;
    } 
         
}


?>