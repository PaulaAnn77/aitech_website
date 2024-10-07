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

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search Users Page</title>
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
            <a class="nav-link" href="posts_page.php">Member Posts</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="profile_update.php">Profile Settings</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Sign Out</a>
          </li>    
        </ul>
      </div>
    </nav>
  </header>
<main>


<?php 

if(isset($_GET['searchTerm']) && !empty(trim($_GET['searchTerm']))) {
    $searchTerm = trim($_GET['searchTerm']);
    $stmt = $con->prepare("SELECT userID, userName FROM Users WHERE userName LIKE ?");
    $searchTerm = "%".$searchTerm."%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        
        // Display a list of users found
        echo '<div style="border: 2px solid black; outline-style: double; padding: 10px; margin: 0 auto; width: 400px; text-align: center; font-size: 25px; margin-top: 30px;">';
        echo "<p>Matching username(s) found. Please select a username:<p><br>";
      
        while ($row = $result->fetch_assoc()) {
            echo '<div style="text-align: center;">';
            echo "<a href='profile_viewer.php?searchedUserID=" . $row['userID'] . "'>" . $row['userName'] . "</a><br><br>";
            echo '</div>';
        }  

        $stmt->close();
    } else {
        echo '<script>alert("Username not found."); window.location = "posts_page.php";</script>';
    }
} else {
    echo 'No search term specified. <a href="posts_page.php">Go back</a>';
}

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
