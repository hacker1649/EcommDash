<?php

include "./db_connection/connection.php"; // Include the database connection file

session_start();

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
  $product_id = $_GET['id'];

  // Update the user status to 0 for the specified user ID
  $query = "UPDATE tbl_product SET product_status = 0 WHERE product_id = $product_id";

  if ($conn->query($query) === TRUE) {
    // Set a session message for successful deletion
    $_SESSION['success'] = "Product blocked successfully!";
  } else {
    // Error handling
    $_SESSION['error'] = "Error blocking product: " . $conn->error;
  }
}

// Redirect back to the main page
header("Location: product_list.php");
exit;

?>
