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
$searchedUserID = $_SESSION['searchedUserID'];

if(isset($_GET['searchedUserID'])) {
  $searchedUserID = $_GET['searchedUserID'];
  $sql = "SELECT * FROM Users WHERE userID = ?";
  $stmt = $con->prepare($sql);
  $stmt->bind_param("i", $searchedUserID);
  $stmt->execute();
  $result = $stmt->get_result();

  if($result->num_rows > 0) {
    $searchedrow = $result->fetch_assoc();
    $profileImage = $searchedrow['userImage'];
    $profileName = $searchedrow['userName'];
  } else {
    echo "Item not found.";
  }
} else {
  echo "No item specified.";
}     

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Viewing Page</title>
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
            <h1>Hello <?php echo $user_data['userName']; ?>.<br><br>Welcome to <?php echo $searchedrow['userName']; ?>'s Profile Page!</h1>
        </div>
</div>



    <div class="row mt-5 text-center">
        <div class="class col">
          <?php echo "<img src='" . $searchedrow['userImage']. "' alt='User Image' class='image-fluid rounded-circle' width= '300'>";?>
        </div>
    </div>

    <div class="row mt-5 text-center">
        <div class="class col">
          <textarea name="userBio" id="userBio" cols="150" rows="8" style="padding: 15px; text-align: center;">  
            <?php echo $searchedrow['userBio']; ?>
          </textarea>
        </div>
    </div>


    <div class="class row mt-5 text-center">
        <div class="class col">
            <h2><?php echo $searchedrow['userName']; ?>'s Posts</h2>
              <div class="post-container" style="padding: 10px;">
                <?php 
                  echo getPostsByUser($con, $searchedUserID); 
                ?>
              </div>
        </div>
    </div>



    <div class="row">
      <div class="col">
      <p><?php 
        ?></p>
      
      <p><?php echo ViewUserPosts($con); ?></p>
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

