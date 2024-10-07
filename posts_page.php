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

    // POSTS php
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
      $userID = $_SESSION['userID'];
  
      if (isset($_POST['action'])) {
          if ($_POST['action'] == 'like') {
            $postID = $_POST['postID'];
            if (updateLikes($con, $postID)) {
              echo "You have liked this post!";
            }
          } elseif ($_POST['action'] == 'comment') {
              if (!empty($_POST["userComment"])) {
                  $userComment = checkInput($_POST['userComment']);
                  $postID = $_POST['postID']; // Retrieve the postID from the form
  
                  if (!empty($userComment)) {
                      if (InsertComment($con, $postID, $userID, $userComment)) {
                          $commentSuccess = "Comment added!";
                      } else {
                          echo "Comment unsuccessful!";
                      }
                  } else {
                      $userCommErr = "Comments can't be empty.";
                  }
              }
          }
      }
  }
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Member Posts Page</title>
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

<div class="row text-center mt-5">
    <div class="col-12">
      <hr width="100%"><br>
      <h1>
        Welcome to AnImal Tech's Community Page.
      </h1><br>
    </div>
  </div>
  
  <div class="row mt-5">
    <div class="col">
      <form method="get" action="search_users.php">
        <input type="text" name="searchTerm" placeholder="Search for a username...">
        <input type="submit" value="search">
      </form>
    </div>
  </div>

  
  <div class="class row mt-5 text-center">
        <div class="class col">
            <h2>Member Posts.</h2>
              <div class="post-container" style="padding: 10px;">

                <?php 
                  echo DisplayAllPosts($con); 
                ?>
              </div>
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
    <p>Enter own details here as required</p>
  </footer>
</div>
</body>
</html>
