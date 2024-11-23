<?php

include "./db_connection/connection.php"; // Include the database connection file

session_start();

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
  $user_id = $_GET['id'];

  // Update the user status to 0 for the specified user ID
  $query = "UPDATE tbl_user SET user_status = 0 WHERE user_id = $user_id";

  if ($conn->query($query) === TRUE) {
    // Set a session message for successful deletion
    $_SESSION['success'] = "User deleted successfully!";
  } else {
    // Error handling
    $_SESSION['error'] = "Error deleting user: " . $conn->error;
  }
}

// Redirect back to the main page
header("Location: manage_user.php");
exit;

?>
