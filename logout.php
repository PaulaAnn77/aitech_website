<!-- 
  Created April 2024
  Last Modified Oct 2024
  @author Paula Farebrother
-->

<?php
session_start();

if(isset($_SESSION['userID']))
{
    unset($_SESSION['userID']);
}

header("Location: login.php");
die;

?>
