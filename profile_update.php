<!-- 
  Created April 2024
  Last Modified Oct 2024
  @author Paula Farebrother
-->

<?php
session_start();

    include("connection.php");
    include("functions.php");

    $user_data = CheckLogin($con);
    $requiredRank = 1; 

  
    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
      
    $userID = $_SESSION["userID"];

    // IMAGES php

    if (isset($_FILES["userImage"])) {
        $result = userImageUpload($con, $_FILES["userImage"], "uploads/", $userID);
    }


    // BIO php

    if (!empty($_POST["userBio"])) {
      // Handle user bio creation
      $userBio = checkInput($_POST['userBio']);
  
      if (!empty($userBio)) {
          // Insert user bio into the database
          if (InsertNewBio($con, $userID, $userBio)) {
              $bioSuccess = "Bio upload successful!";
          } else {
              $bioSuccess = "Bio upload unsuccessful!";
          }
      } else {
          $userBioErr = "Bio submission can't be empty.";
      }
  }
    
    }
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Settings Page</title>
    <link rel="stylesheet" href="ai_pets_css_v3.css"> <!-- Connects CSS sheet -->
    <!-- Connects Bootstrap for CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
      rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
      crossorigin="anonymous">    
    <!-- Bootstrap icons sheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>

<body class="container mt-3"> <!-- Bootstrap layout container -->
  <header> <!-- Responsive navigation bar included in header with logo -->
    <nav class="navbar navbar-expand-sm bg-light navbar-light">
      <a class="navbar-brand" href="#">
        <img src="images/logo.png" alt="AnImal Tech logo." width="150">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="aitech_pets.html">Home</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="profile_page.php">Profile Page</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="logout.php">Sign Out</a>
        </li>    
        </ul>
      </div>
    </nav>
  </header>
<main>
    <div class="row mt-5 text-center">
        <div class="class col">
            <h1>Hello <?php echo $user_data['userName']; ?>, welcome to your Profile Settings Page.</h1>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-1">
            <!-- spacer column -->
        </div>

        <div class="col-4 p-3" style="border: 1px solid black; padding: 10px; margin-bottom: 10px; width:450;">
            <br><h2>Upload a New Profile Image.</h2><br><br>
            <p style="color: red">
              <?php echo $result; ?>
            </p>
            <form method="post" enctype="multipart/form-data">
                    <input id="file" type="file" name="userImage"><br><br>
                    <input id="button" type="submit" value="Add New Image"><br>
            </form>
            <p>
                File size must be less than 5MB.
                Acceptable file types are:
                <ul class="row text-left">
                    <li>JPEG</li>
                    <li>JPG</li>
                    <li>PNG</li>
                    <li>GIF</li>
                </ul>
            </p>
        </div>
        
        <div class="col-1">
            <!-- spacer column -->
        </div>

        <div class="col-6 p-5" style="border: 1px solid black; padding: 10px; margin-bottom: 10px; width:600;"><br>
            <h2>
                Update Your Bio Information.
            </h2><br><br>
            <p style="color: red">
              <?php echo $bioSuccess; ?>
            </p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> <!-- special characters used for security-->
            <label for="userBio"></label><br>
                <textarea id="createBio" name="userBio" placeholder="Type your bio information here." rows="10" cols="60" style="align-self: auto;"></textarea>
                <span class="error"><?php echo $userBioErr;?></span><br><br>
                <input id="button" type="submit" value="Submit Your Bio Information"><br><br>
            </form>
    </div>
  </div>

  <?php 
    if(!CheckRankAccess($requiredRank, $user_data)) {
        header("Location: login.php");
        die;
    }
    ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
crossorigin="anonymous"></script>
<script> src="ai_petsjsv2.js" </script>
</main>
<div class="row mt-5">
  <footer class="footer-end">
    <p>Enter personal details here as required</p>
  </footer>
</div>
</body>
</html>

