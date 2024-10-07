<!-- 
  Created April 2024
  Last Modified Oct 2024
  @author Paula Farebrother
-->

<?php

// Displays a list of all users
function DisplayAllUsers($con)
{

// Submits an SQL query to the database
$sql = "SELECT userID, userName FROM Users";
$result = $con->query($sql);

    // Check if results were found
    if ($result->num_rows > 0) {
        echo "<br>" . "We have results!" . "<br>";
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<br> User ID: " . htmlentities($row['userID']) . "- User Name: " . htmlentities($row['userName']) . "<br>";
        }
    }
    else {
        echo "0 results found." . "<br>";
    }
}

// Ensures users are logged in before viewing page
function CheckLogin($con)
{
    if(isset($_SESSION['userID']))
    {
       $userID = $_SESSION['userID'];
       $query = "SELECT * FROM Users WHERE userID = '$userID' limit 1";
        
       $result = mysqli_query($con, $query);
       if($result && mysqli_num_rows($result) > 0)
       {
           $user_data = mysqli_fetch_assoc($result);
           return $user_data;
       }
   }
     
   //redirect to login
   header("Location: login.php");
   die;
}

// Allows Tiers of users to be set (0 = Admin, 1 = End User)
function CheckRankAccess($requiredRank, $user_data)
{
    $userRank = $user_data['userRank'];
    if($userRank <= $requiredRank)
    {
        return true;
    }
    else {
        return false;
    }
}

// Sanitises data 
function checkInput($data) {
    $data = trim($data);
    return $data;
}

// Retrieves usernames for unique check
function getUserNameData($con) 
{
    $query = "SELECT userName FROM Users";
    $result = mysqli_query($con, $query);

    $userNameData = array(); // Initialize an array to store all user names

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $userNameData[] = $row['userName']; // Store each user name in the array
        }
    }
    return $userNameData;
}

// Ensures usernames are unique
function CheckUserName($userName, $userNameData)
{
    foreach ($userNameData as $user) {
        if ($user === $userName) {
            return false; // Username already exists
        }
    }
    return true; // Username is unique
}

// Adds new user details to the database
function InsertNewUser($con, $userRank, $userName, $userEmail, $userPassword) {

    //Prepare and bind
    $sqlQuery = $con->prepare("INSERT INTO Users (userRank, userName, userEmail, userPassword)
    VALUES(?, ?, ?, ?)");
    $sqlQuery->bind_param("isss", $userRank, $userName, $userEmail, $userPassword);

    $QuerySuccessful = true;
    if(!$sqlQuery->execute()) {
        $QuerySuccessful = false;
    }
    $sqlQuery->close();

    return $QuerySuccessful;
}

// Generates Tokens for security against Cross Site Request Forgery
function generateRandomString($length) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $randomString = '';
    $max = strlen($characters) - 1;

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $max)];
    }
    return $randomString;
}

function GenerateToken() {
    $_SESSION['token'] = generateRandomString(25);
}

function ValidateToken($formToken) {
    if($formToken === $_SESSION['token']) {
        return True;
    }
    return false;
}

// Uses Prepared Statements to add a User Post.
function InsertNewPost($con, $userID, $userPost, $image_path) {
    $sqlQuery = $con->prepare("INSERT INTO Posts (userID, userPost, userPostImage) VALUES(?, ?, ?)");
    $sqlQuery->bind_param("iss", $userID, $userPost, $image_path);

    $QuerySuccessful = true;
    if(!$sqlQuery->execute()) {
        $QuerySuccessful = false;
    }
    $sqlQuery->close();

    return $QuerySuccessful;
}

// Retrieves users own posts
function getPostData($con)
{
    $userID = $_SESSION['userID'];
    
    // Submit an SQL query to the database
    $sql = "SELECT * FROM Posts WHERE userID = '$userID' ORDER BY postID DESC";
    $result = $con->query($sql);

    // Check if results were found
    if ($result->num_rows > 0) {
        echo '<div style="border: 2px solid black; padding: 10px; 
            margin-bottom: 10px; display: block; height: 700px; overflow-y: auto;">';
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo '<div style="border: 2px solid black; padding: 10px; 
                margin-bottom: 10px;">';           
            // Min/max width set to ensure image size consistent
            echo '<div style="min-width: 200px; margin-bottom: 10px">';
            echo '<img src="' . $row['userPostImage'] . '" alt="User Post Image" 
                class="img-fluid rounded-circle" style="max-width: 200px;">';
            echo '</div>';
            
            // Separate div for post content
            echo '<div>';
            echo "Post Date: " . htmlentities($row['userPostDate']) . "<br><br>";
            echo '<div>' . htmlentities($row['userPost']) . '</div>';
            echo '</div>';
            
            echo '</div>';
        }
    } else {
        echo "0 posts found." . "<br>";
    }
}

// Displays all user posts and associated comments, includes a like form
function DisplayAllPosts($con)
{
    // Submit an SQL query to the database using a JOIN query
    $sql = "SELECT Posts.*, 
        DATE_FORMAT(Posts.userPostDate, '%d/%m/%y') AS formattedDate, 
        Users.userName,
        Comments.userComment AS comment,
        DATE_FORMAT(Comments.commentDate, '%d/%m/%y') AS commentDate,
        Posts.likeCounter
        FROM Posts 
        INNER JOIN Users ON Posts.userID = Users.userID 
        LEFT JOIN Comments ON Posts.postID = Comments.postID 
        ORDER BY userPostDate DESC";    
    $result = $con->query($sql);

    // Check if results were found
    if ($result->num_rows > 0) {
        echo '<div style="border: 2px solid black; padding: 10px; 
            margin-bottom: 10px; display: block; height: 1000px; overflow-y: auto;">';
        
        // Initialize an array to store all posts
        $posts = [];

        // Output data of each post
        while ($row = $result->fetch_assoc()) {
            $posts[$row['postID']][] = $row;
        }

		// for loop includes $postID as a key
		foreach ($posts as $postID => $postComments) { 
		   
            // Display the post content
            echo '<div style="border: 2px solid black; padding: 10px; margin-bottom: 10px;">';           
            echo "<br> Username: " . htmlentities($postComments[0]['userName']) . "<br><br>";
            echo '<div style="min-width: 200px; margin-bottom: 10px">';
            echo '<img src="' . $postComments[0]['userPostImage'] . '" alt="User Post Image" 
                class="img-fluid rounded-circle" style="max-width: 200px;">';
            echo '</div>';
            echo '<div>';
            echo "Post Date: " . htmlentities($postComments[0]['formattedDate']) . "<br><br>";
            echo '<div>' . htmlentities($postComments[0]['userPost']) . '</div>';
            echo '<form method="post">';
            echo "<input type='hidden' name='postID' value='" . $postID . "'>"; 
            echo '<br><button type="submit" name="action" value="like" id="upvote-button">
            <div onmouseover="mOver(this)" onmouseout="mOut(this)">
            👍</div></button>';
            echo '<div>Likes: ' . $postComments[0]['likeCounter'] . '</div><br>';
            echo '</form>';
            echo '</div>';

            // Display each comment for the post
            foreach ($postComments as $comment) {
                if ($comment['comment'] !== null) {
                    echo '<div style="border: 2px solid black; padding: 10px; margin-bottom: 10px;">';
                    echo "Comment Date: " . htmlentities($comment['commentDate']) . "<br><br>";
                    echo '<div>' . htmlentities($comment['comment']) . '</div>';
                    echo '</div>';
                }
            }
			// Adds comment form
            echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='post'>";
            echo "Comment: <input type='text' name='userComment'>";	
            echo "<input type='hidden' name='postID' value='" . $postID . "'>"; 		
            echo "<input type='hidden' name='userID' value='" . $_SESSION['userID'] . "'>"; 
            echo "<input type='hidden' name= 'action' value='comment'>";
            echo "<input type='submit' value='Add Comment'>";
            echo "</form><br><br>";  
            echo "</div>";
        } echo '</div>';
        echo '<script>
            function mOver(obj) {
                obj.innerHTML = "😊"
            }
            function mOut(obj) {
                obj.innerHTML = "👍"
            }
            </script>';
    } else {
        echo "0 posts found." . "<br>";
    }
}

// Allows admins to view all posts, delete posts and delete comments.  
function DisplayAllPostsAdmin($con) {
    // Submit an SQL query to the database using a JOIN query
    $sql = "SELECT Posts.*, 
        DATE_FORMAT(Posts.userPostDate, '%d/%m/%y') AS formattedDate, 
        Users.userName,
        Comments.commentID, 
        Comments.userComment AS comment,
        DATE_FORMAT(Comments.commentDate, '%d/%m/%y') AS commentDate
        FROM Posts 
        INNER JOIN Users ON Posts.userID = Users.userID 
        LEFT JOIN Comments ON Posts.postID = Comments.postID
        ORDER BY userPostDate DESC";
    $result = $con->query($sql);

    // Check if results were found
    if ($result->num_rows > 0) {
        echo '<div style="border: 2px solid black; padding: 10px; 
            margin-bottom: 10px; display: block; height: 1000px; overflow-y: auto;">';
        
        // Initialize an array to store all posts
        $posts = [];

        // Output data of each post
        while ($row = $result->fetch_assoc()) {
            $posts[$row['postID']][] = $row; 
        }

        // Display each post and its comments
        foreach ($posts as $postID => $postComments) {
            // Display the post content
            echo '<div style="border: 2px solid black; padding: 10px; margin-bottom: 10px;">';           
            echo "<br> Username: " . htmlentities($postComments[0]['userName']) . "<br><br>";
            echo '<div style="min-width: 200px; margin-bottom: 10px">';
            echo '<img src="' . htmlentities($postComments[0]['userPostImage']) . '" alt="User Post Image" class="img-fluid rounded-circle" style="max-width: 200px;">';
            echo '</div>';
            echo '<div>';
            echo "Post Date: " . htmlentities($postComments[0]['formattedDate']) . "<br><br>";
            echo '<div>' . htmlentities($postComments[0]['userPost']) . '</div><br>';
            
            // Add a delete button for the post
            echo '<form method="post">';
            echo '<input type="hidden" name="postID" value="' . $postID . '">'; 
            echo '<button type="submit" name="deletePost" value="' . $postID . '">Delete Post</button>';
            echo '</form><br>';
            echo '</div>';

            // Display each comment for the post
            foreach ($postComments as $comment) {
                if (!empty($comment['comment'])) {
                    echo '<div style="border: 2px solid black; padding: 10px; margin-bottom: 10px;">';
                    echo "Comment Date: " . htmlentities($comment['commentDate']) . "<br><br>";
                    echo '<div>' . htmlentities($comment['comment']) . '</div><br>';
                    // Add a delete button for the comment -- check if commentID is present
                    if (isset($comment['commentID']) && $comment['commentID'] !== null) {
                        echo '<form method="post">';
                        echo '<input type="hidden" name="commentID" value="' . htmlentities($comment['commentID']) . '">'; 
                        echo '<button type="submit" name="deleteComment" value="' . htmlentities($comment['commentID']) . '">Delete Comment</button>';
                        echo '</form>';
                    }
                    echo '</div>';
                }
            }
            echo '</div>'; 
        }
        echo '</div>'; 
    } else {
        echo "0 posts found." . "<br>";
    }
}

// Increments likeCounter in database for each post when clicked 
function updateLikes($con, $postID) {
    $increment = 1; // Set the increment value to 1
    // Prepare and bind with the increment parameter
    $sqlQuery = $con->prepare("UPDATE Posts SET likeCounter = likeCounter + ? WHERE postID = ?");
    $sqlQuery->bind_param("ii", $increment, $postID);  

    $QuerySuccessful = true;
    if (!$sqlQuery->execute()) {
        $QuerySuccessful = false;
    }
    $sqlQuery->close();

    return $QuerySuccessful;
}

// Allows user to create a comment attached to a post and saves to database
function InsertComment($con, $postID, $userID, $userComment) {
    $QuerySuccessful = true;

    // Prepare and bind parameters
    $sqlQuery = $con->prepare("INSERT INTO Comments (postID, userID, userComment) VALUES (?, ?, ?)");
    
    if (!$sqlQuery) {
        echo "Error preparing statement: " . $con->error;
        $QuerySuccessful = false;
    } else {
        $sqlQuery->bind_param("iis", $postID, $userID, $userComment);
        
        if (!$sqlQuery->execute()) {
            echo "Error executing statement: " . $sqlQuery->error;
            $QuerySuccessful = false;
        }
        $sqlQuery->close();
    }
    return $QuerySuccessful;
}

// Allows user to upload an image, stores in a file with filepath in database
function userImageUpload($con, $imageData, $uploadedDir, $userID) {
    $target_dir = $uploadedDir;
    $target_file = $target_dir . basename($imageData["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($imageData["tmp_name"]);
    if ($check === false) {
        return "File is too large.";
    }

    if (file_exists($target_file)) {
        return "Sorry, filename already exists.";
    }

    if ($imageData["size"] > 5000000) {
        return "Sorry, this file is too large.";
    }

    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        return "Sorry, only JPG/JPEG, PNG, and GIF files are allowed.";
    }

    if ($uploadOk == 1) {
        if ($imageData["error"] == 0) {
            if (move_uploaded_file($imageData["tmp_name"], $target_file)) {
                $userImage = $target_file;
                
                $stmt = $con->prepare("UPDATE Users SET userImage = ? WHERE UserID = ?");
                $stmt->bind_param('si', $userImage, $userID);

                if ($stmt->execute()) {
                    return "Image added successfully.";
                } else {
                    return "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                if (!is_writable($target_dir)) {
                    return "Error: The target directory is not writable";
                }
                if (!is_dir($target_dir)) {
                    return "Error: The target directory does not exist.";
                }
            }
        } else {
            return "File upload error. Error code: " . $imageData["error"];
        }
    } else {
        return "File did not pass the upload checks.";
    }
}

// Retrieves and formats user uploaded profile image
function getUserImage($con) {
    
    $userID = $_SESSION['userID'];

    $sql = "SELECT userImage FROM Users WHERE userID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
            
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<img src='" . $row['userImage']. "' alt='User Image' 
            class='image-fluid rounded-circle' width= '300'>";
        } else {
            echo "<img src= 'uploads/profile_def_img.jpg' alt= 'Default user profile image' 
                class='image-fluid rounded-circle' width= '300'>";
        } 
}

// Retrieves and formats user uploaded post image
function userPostImageUpload($con, $imageData, $uploadedDir, $userID) {
    $target_dir = $uploadedDir;
    $target_file = $target_dir . basename($imageData["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($imageData["tmp_name"]);
    if ($check === false) {
        return "File is not an image.";
    }

    if (file_exists($target_file)) {
        return "Sorry, filename already exists.";
    }

    if ($imageData["size"] > 5000000) {
        return "Sorry, this file is too large.";
    }

    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        return "Sorry, only JPG/JPEG, PNG, and GIF files are allowed.";
    }

    if ($uploadOk == 1) {
        if ($imageData["error"] == 0) {
            if (move_uploaded_file($imageData["tmp_name"], $target_file)) {
                $userImage = $target_file;
                
                $stmt = $con->prepare("UPDATE Posts SET userPostImage = ? WHERE UserID = ?");
                $stmt->bind_param('si', $userImage, $userID);

                if ($stmt->execute()) {
                    return "Image added successfully!";
                } else {
                    return "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                if (!is_writable($target_dir)) {
                    return "Error: The target directory is not writable";
                }
                if (!is_dir($target_dir)) {
                    return "Error: The target directory does not exist.";
                }
            }
        } else {
            return "File upload error. Error code: " . $imageData["error"];
        }
    } else {
        return "File did not pass the upload checks.";
    }
}

// Adds user bio to database
function InsertNewBio($con, $userID, $userBio) {
    $sql = "UPDATE Users SET userBio = ? WHERE userID = ?";
    $sqlQuery = $con->prepare($sql);
    $sqlQuery->bind_param("si", $userBio, $userID);
    
    $querySuccessful = true;
    if(!$sqlQuery->execute()) {
        $querySuccessful = false;
    }
    $sqlQuery->close();
    
    return $querySuccessful;
}

// Retrieves user posts: image, post date, post data for profile viewer page (so other users can view)
function getPostsByUser($con, $searchedUserID) {
    $sql = "SELECT * FROM Posts WHERE userID = ? ORDER BY postID DESC";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $searchedUserID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        echo "Error fetching posts: " . $con->error;
        exit();
    }

    if ($result->num_rows > 0) {
        echo '<div style="border: 2px solid black; padding: 10px; margin-bottom: 10px; display: block; height: 700px; overflow-y: auto;">';

        while ($row = $result->fetch_assoc()) {
            echo '<div style="border: 2px solid black; padding: 10px; margin-bottom: 10px;">';
            echo '<div style="min-width: 200px; margin-bottom: 10px">';
            echo '<img src="' . $row['userPostImage'] . '" alt="User Post Image" class="img-fluid rounded-circle" style="max-width: 200px;">';
            echo '</div>';

            echo '<div>';
            echo "Post Date: " . htmlentities($row['userPostDate']) . "<br><br>";
            
            echo '<div>' . (isset($row['userPost']) ? htmlentities($row['userPost']) : '') . '</div>';
            echo '</div>';

            echo '</div>';
        }

        echo '</div>';
    } else {
        echo "0 posts found.";
    }
}


?>
