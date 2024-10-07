<!-- 
  Created April 2024
  Last Modified Oct 2024
  @author Paula Farebrother
-->

<?php

$dbhost = <"enter hose details">;
$dbuser = <"enter user details">;
$dbpass = <"enter pass">;
$dbname = <"enter name">;

if (!$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname)) {
    die("Failed to connect!");
}

?>
