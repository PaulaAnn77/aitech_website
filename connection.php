<!-- 
  Created April 2024
  Last Modified on 3rd May 2024
  @author Paula Farebrother (2309693)
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
