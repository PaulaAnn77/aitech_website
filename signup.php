<!-- 
  Created April 2024
  Last Modified Oct 2024
  @author Paula Farebrother
-->

<?php
session_start();

include("connection.php");
include("functions.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Sets variables to empty
    $nameErr1 = $nameErr2 = "";

    $userName = checkInput($_POST['userName']); // Sanitize the input first

    // Returns an error if the username is empty
    if (empty($userName)) {
        $nameErr1 = "Username is required";
    } elseif (!preg_match("~[0-9a-zA-Z]+~", $userName)) {
        $nameErr2 = "Please enter a valid username.";
    }

    if (!empty($userName)) {
        $userNameData = getUserNameData($con); //  fetch user data
        if (CheckUserName($userName, $userNameData)) { // check if username is unique
            $_SESSION['userName'] = $userName;
            header("Location: signuppg2.php");
            die();
        } else {
          $UNError = "Sorry, that username is already taken, please try again.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Page</title>
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
            <a class="nav-link" href="login.php">Sign In</a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
<main>

<div class="row mt-5 text-center">
    <div class="class col">
        <h1>Welcome to the Registration Page!</h1>
    </div>
</div>

<div class="row mt-5 text-center">
    <div class="col">
      <h2>Please enter a username.</h2>
        <div id="box">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"><!-- special characters used for security-->
                <label for="UserName"></label><br>
                <input id="UserName" type="text" name="userName" placeholder="JoeBloggs4" size="30">
                <?php if (isset($UNError)) { echo "<br>" . $UNError; } ?>
                <span class="error"><?php echo $nameErr1; echo $nameErr2;?></span><br><br>

                <!-- confirmation password check box here -->

                <input id="button" type="submit" value="Next"><br><br>
            </form>
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
