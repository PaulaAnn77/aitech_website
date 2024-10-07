<!-- 
  Created April 2024
  Last Modified Oct 2024
  @author Paula Farebrother
-->

<?php
session_start();

    include("connection.php");
    include("functions.php");

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {

        // Sets variables to empty
        $nameErr1 = $nameErr2 = $emailErr1 = $emailErr2 = $passwordErr = "";

        if(empty($_POST["userEmail"])) {
            $emailErr1 = "Email is required";
            
        } elseif (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {    
            $emailErr2 = "Please enter a valid email address.";
        
        } elseif(empty($_POST["userPassword"])) {
            $passwordErr = "Password is required";
        }  

    		$userPassword = $_POST["userPassword"];
        $userName = $_SESSION['userName'];
        // If something was posted - data sanitized
        $userEmail = checkInput($_POST['userEmail']);
        // Password is hashed for additional security
        $userPassword = password_hash($userPassword, PASSWORD_DEFAULT);


        if(!empty($userEmail) && !empty($userPassword)) 
            {
                // Saves to database - userRank is set to 1 by default, admins can only be set up by other admins.
                if(InsertNewUser($con, 1, $userName, $userEmail, $userPassword))

                {
                    header("Location: login.php");
                    die;
                }
                else
                {
                    echo "Query unsuccessful!";
                    die();
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
        <h1>Next, please update your details.</h1>
    </div>
</div>

<div class="row mt-5 text-center">
    <div class="col">
        <div id="box">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"><!-- special characters used for security-->
                <div class = "error" style="font-size: 18px; margin: 10px; color: darkred;">* required field</div><br>
                <label for="UserEmail">Email:</label><br>
                <input id="UserEmail" type="text" name="userEmail" placeholder="joeBloggs4@webmail.com" size="30">
                <span class="error">* <?php echo $emailErr;?></span><br><br>
                <label for="UserPassword">Password:</label><br>
                <input id="UserPassword" type="password" name="userPassword" placeholder="Letters, numbers, characters..." size="30">
                <span class="error">* <?php echo $passwordErr;?></span><br><br>

                <!-- confirmation password check box here -->

                <input id="button" type="submit" value="Register"><br><br>
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
