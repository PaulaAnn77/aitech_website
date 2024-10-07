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
      
      $userID = $_SESSION['userID'];

      // POSTS php
      if (!empty($_POST["userPost"])) {
        // Handle user post creation
        $userPost = checkInput($_POST['userPost']);
      
        if (!empty($userPost)) {
          // Handle image upload
          $image_path = "uploads_posts/" . $_FILES["postImage"]["name"];
          move_uploaded_file($_FILES["postImage"]["tmp_name"], $image_path);

          if (!empty($userPost)) {
              // Insert user post into the database
              if (InsertNewPost($con, $userID, $userPost, $image_path)) {
                  $postSuccess = "Post successful!";
              } else {
                  echo "Post unsuccessful!";
              }
          } else {
              $userPostErr = "Posts can't be empty.";
          }
        }
      }
    }

    if(!CheckRankAccess($requiredRank, $user_data)) {
        header("Location: login.php");
        die;
    }
?>
    
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Page</title>
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
          <li class="nav-item" style="text-align:right;">
            <a class="nav-link" href="indexadmin.php">Admin Page</a>
          </li>    
        </ul>
      </div>
    </nav>
  </header>

 
<main>
    <div class="row mt-5 text-center">
        <div class="class col">
            <h1>Hello <?php echo $user_data['userName']; ?>.<br><br> 
              Welcome to your Profile Page!</h1>
        </div>
    </div>
   <div class="class row mt-5 text-center" class="userImage">
        <div class="class col">
            <p>
              <?php getUserImage($con);?>
            </p>
        </div>
    </div>


    <div class="class row mt-5 text-center">
        <div class="class col">
            <h2>
              Bio
            </h2>
            <form>
              <textarea name="userBio" id="userBio" cols="150" rows="8" style="padding: 15px; text-align: center;">  
                <?php echo $user_data['userBio']; ?>
              </textarea>
            </form>
        </div>
    </div>
        
    <div class="class row mt-5 text-center">
        <div class="class col">
            <h2><?php echo $user_data['userName']; ?>'s Posts</h2>
              <div class="post-container" style="padding: 10px;">

                <?php 
                  echo getPostData($con); 
                ?>
              </div>
        </div>
    </div>


  <div class="row text-center mt-5">
    <div class="col-12">
      <hr width="100%"><br>
      <h2>
        Create a new post.
      </h2><br>
    </div>
  </div>

<div class="row mt-5">
  <div class="col-3">
    <!-- spacer column -->
  </div>
  <div class="col-6 p-3" style="border: 1px solid black; padding: 10px; margin-bottom: 10px; width:650;">
            <br><h3>Upload a Post Image.</h3><br>
            <form method="post" enctype="multipart/form-data"action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> <!-- special characters used for security-->
              <label for="userPost"></label><br>
              <input id="file" type="file" name="postImage"><br><br>
              <br><h3>Create Your Post.</h3><br>
              <textarea id="createPost" name="userPost" placeholder="Type your post here." rows="10" cols="80"></textarea>
              <span class="error"><?php echo $userPostErr;?></span><br><br>
              <input id="button" type="submit" value="Create"><br><br>
            </form>
            <div style="border: 1px solid black; padding: 10px; margin-bottom: 10px;">
            <p>
                File size for images must be less than 5MB.
                Acceptable file types are:
                <ul class="row" style="text-align: left;">
                    <li>JPEG</li>
                    <li>JPG</li>
                    <li>PNG</li>
                    <li>GIF</li>
                </ul>
            </p>
            </div>
        </div>
</div>
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
