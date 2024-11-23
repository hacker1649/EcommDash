<?php

include '../../db_connection/connection.php';
session_start();

if (!isset($_SESSION['userLoggedIn']) || $_SESSION['userLoggedIn'] !== true) {
  header("Location: ../login.php");
  exit;
}

$item_id = isset($_GET['item_id']) ? (int) $_GET['item_id'] : null;
$quantity = isset($_GET['quantity']) ? (int) $_GET['quantity'] : null;

if ($item_id && $quantity && $quantity > 0) {
  // Fetch product price for the item
  $item_query = "SELECT product_price, cart_id FROM tbl_cart_item WHERE item_id = $item_id";
  $item_result = mysqli_query($conn, $item_query);
  $item = mysqli_fetch_assoc($item_result);

  if ($item) {
    $product_price = $item['product_price'];
    $t_product_price = $product_price * $quantity;

    // Update quantity and total product price in cart item
    $update_query = "UPDATE tbl_cart_item SET quantity = $quantity, t_product_price = $t_product_price WHERE item_id = $item_id";
    mysqli_query($conn, $update_query);

    // Recalculate total cart amount
    $cart_id = $item['cart_id'];
    $total_query = "SELECT SUM(t_product_price) AS total_amount FROM tbl_cart_item WHERE cart_id = $cart_id";
    $total_result = mysqli_query($conn, $total_query);
    $cart_total = mysqli_fetch_assoc($total_result)['total_amount'];

    // Update total amount in tbl_cart
    $update_cart = "UPDATE tbl_cart SET total_amount = $cart_total WHERE cart_id = $cart_id";
    mysqli_query($conn, $update_cart);
  }
} else {
  $user_id = $_SESSION['user_id'] ?? 0; // Get the user_id from session
  // Check if a cart exists for the user
  $sql_check_cart = "SELECT cart_id FROM tbl_cart WHERE user_id = $user_id AND cart_status = 1";
  $result_check_cart = $conn->query($sql_check_cart);

  if ($result_check_cart->num_rows > 0) {
    $cart_id = $result_check_cart->fetch_assoc()['cart_id'];

    // Delete the product from the cart_item table
    $sql_remove_item = "DELETE FROM tbl_cart_item WHERE cart_id = $cart_id AND item_id = $item_id";
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
}

header("Location: add_to_cart.php");
exit;
