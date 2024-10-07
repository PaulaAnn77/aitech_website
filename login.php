<!-- 
  Created April 2024
  Last Modified Oct 2024
  @author Paula Farebrother
-->

<?php
session_start();

    include("connection.php");
    include("functions.php");

    // Checks if user has clicked submit to login
    if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['token']) && ValidateToken($_POST['token']))
    {

        // Sets variables to empty
        $nameErr1 = $nameErr2 = $passwordErr = "";

        // returns an error to each box if nothing is entered and uses preg_match to ensure correct format
        if(empty($_POST["userName"])) {
            $nameErr1 = "Username is required"; 

        } elseif (!preg_match("~[0-9a-zA-Z]+~", $userName)) {
            $nameErr2 = "Please enter a valid username.";
        
        } elseif(empty($_POST["userPassword"])) {
            $passwordErr = "Password is required";
        }

        // If something was posted
        $userName = checkInput($_POST['userName']);

        if(!empty($userName) && !empty($_POST['userPassword'])) 
        {
            // Read from database using Prepared statement
            $sqlQuery = $con->prepare("SELECT * FROM Users WHERE userName = ? LIMIT 1");
            // bind posted userName to the query
            $sqlQuery->bind_param("s", $userName);

            if($sqlQuery->execute()) {
                $result = $sqlQuery->get_result();
                if($result) {
                    $user_data = $result->fetch_assoc(); // fetch assoc data
                    
					
					if($user_data) {
                        // data is now an array and can be accessed using index
                       if (password_verify($_POST['userPassword'], $user_data['userPassword'])) {		
							
                            echo "we have a match";
							              // Proceed with login
                            $_SESSION['userID'] = $user_data['userID'];
                            header("Location: indexadmin.php");
                            die;
                        }
						else {
							echo $_POST['userPassword'];
							echo "Passwords did not match";
						}
                    }
                    else {
                        echo "Wrong username or password.";
                    } 
                } 
				else {
					echo "No Result";
				}
            }
			else {
				
				echo "sqlQuery did not run";
			}			
        } 
    }
GenerateToken();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In Page</title>
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
            <a class="nav-link" href="signup.php">Register</a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
    <div class="row mt-5 text-center">
        <h1>Welcome to the Sign In Page</h1>
    </div>

    <div class="row mt-5 text-center">
        <div id="box" class="row">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> <!-- special characters used for security-->
                <div class="error" style="font-size: 18px; margin: 10px; color: darkred;">* required field</div><br>
                <label for="UserName">Username:</label><br>
                <input id="UserName" type="text" name="userName" placeholder="JoeBloggs4" size="30">
                <span class="error">* <?php echo $nameErr;?></span><br><br>
                <label for="Password">Password:</label><br>
                <input id="UserPassword" type="password" name="userPassword" placeholder="Password...." size="30">
                <span class="error">* <?php echo $passwordErr;?></span><br><br>
                <input type="hidden" name='token' value="<?=$_SESSION['token']?>"></type>
                <input id="button" type="submit" value="Sign In"><br><br>
            </form>
        </div>
    </div>
    <div class="row mt-5 text-center">
        <div class="row">
            <h4>Don't have an account?</h4>
            <a href="signup.php">Click here to Register</a><br><br>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
crossorigin="anonymous"></script>
<script> src="ai_petsjsv2.js" </script>
</main>
<div class="row mt-5">
  <footer class="footer-end">
    <p>Add your own details here as required</p>
  </footer>
</div>
</body>
</html>
