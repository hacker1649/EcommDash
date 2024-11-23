<?php

include '../../db_connection/connection.php'; // Include the database connection file

session_start();

// Check if the product_id is sent via POST request
if (isset($_POST['product_id'])) {
  $product_id = $_POST['product_id'];
  $user_id = $_SESSION['user_id'] ?? 0; // Get the user_id from session
  // Check if a cart exists for the user
  $sql_check_cart = "SELECT cart_id FROM tbl_cart WHERE user_id = $user_id AND cart_status = 1";
  $result_check_cart = $conn->query($sql_check_cart);

  if ($result_check_cart->num_rows > 0) {
    $cart_id = $result_check_cart->fetch_assoc()['cart_id'];

    // Delete the product from the cart_item table
    $sql_remove_item = "DELETE FROM tbl_cart_item WHERE cart_id = $cart_id AND product_id = $product_id";
    if ($conn->query($sql_remove_item)) {
      // Optionally update the cart's total amount after removing the item
      $sql_update_total = "UPDATE tbl_cart SET total_amount = (SELECT SUM(t_product_price) FROM tbl_cart_item WHERE cart_id = $cart_id) WHERE cart_id = $cart_id";
      $conn->query($sql_update_total);

      // Check if the cart is empty after removal
      $sql_check_empty_cart = "SELECT COUNT(*) as item_count FROM tbl_cart_item WHERE cart_id = $cart_id";
      $result_check_empty_cart = $conn->query($sql_check_empty_cart);
      $item_count = $result_check_empty_cart->fetch_assoc()['item_count'];

      if ($item_count == 0) {
        // Delete the cart if empty
        $sql_delete_cart = "DELETE FROM tbl_cart WHERE cart_id = $cart_id";
        $conn->query($sql_delete_cart);
      }

      echo "success"; // Respond back to indicate success
    } else {
      echo "error"; // If there's an issue with deletion
    }
  } else {
    echo "error"; // No active cart found for this user
  }
} else {
  echo "error"; // No product ID sent

}

// Redirect to the cart page
header('Location: add_to_cart.php');
exit;
