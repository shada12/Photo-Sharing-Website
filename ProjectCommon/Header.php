

<!DOCTYPE html>
<html lang="en" style="position: relative; min-height: 100%;">
    <head>
        <title>Online Course Registration</title>
        <meta charset="utf-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="AlgCommon/Contents/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="AlgCommon/Contents/AlgCss/Site.css" rel="stylesheet" type="text/css"/>
        <link href="AlgCommon/Contents/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="./AlgCommon/Contents/css/Site.css" rel="stylesheet" type="text/css"/>
        <link href="" rel="stylesheet" type="text/css"/>
		
        <script src="AlgCommon/Scripts/jquery-2.2.4.min.js" type="text/javascript"></script>
        <script src="AlgCommon/Contents/js/bootstrap.min.js" type="text/javascript"></script>
    </head>
    <body style="padding-top: 50px; margin-bottom: 60px;">
        <nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" 
                            data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li><a class="navbar-brand" style="padding: 10px" href="http://www.algonquincollege.com">
                                <img src="AlgCommon/Contents/img/AC.png" 
                                     alt="Algonquin College" style="max-width:100%; max-height:100%;"/>
                            </a></li>
                        <li class="active"><a href="Index.php">Home </a></li>
                        <li><a href="MyFriends.php">My Friends</a></li>
                        <li><a href="MyAlbums.php">My Albums</a></li>
                        <li><a href="MyPictures.php">My Pictures</a></li>
                        <li><a href="UploadPictures.php">Upload Pictures</a></li>
                        <?php
                        if(isset($_SESSION["valid"])){
                            echo '<li><a href="Logout.php">Log Out</a></li>';
                        }
                        else
                        {
                            echo '<li><a href="Login.php">Log In</a></li>';
                        }
                        ?>
                        <!--<li><a href="Login.php">Log In</a></li>!-->    
                    </ul>
                </div>
            </div>  
        </nav>
