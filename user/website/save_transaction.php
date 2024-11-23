<?php

include '../../db_connection/connection.php'; // Include your database connection

session_start();

if (!isset($_SESSION['userLoggedIn']) || $_SESSION['userLoggedIn'] !== true) {
  http_response_code(403); // Forbidden
  exit;
}

$user_id = $_SESSION['user_id'] ?? "";
$cart_id = $_SESSION['cart_id'] ?? "";

// Check if transaction details are in the GET request
if (isset($_GET['transaction_details'])) {
  $transactionDetails = $_GET['transaction_details'];

  // Sanitize the transaction details
  $transactionDetails = $conn->real_escape_string($transactionDetails);

  //payment mode 
  $paymentMethod = 'paypal';

  // Update cart status to 3, meaning completed, and update the payment method
  $update_status = "UPDATE tbl_cart SET cart_status = 3, payment_mode = '$paymentMethod' WHERE cart_id = $cart_id";
  if ($conn->query($update_status) === TRUE) {
    $current_time = time();

    // Fetching total_amount value directly from database
    $fetch_amount = "SELECT total_amount FROM tbl_cart WHERE cart_id = $cart_id";
    $fetch_result = $conn->query($fetch_amount);
    if ($fetch_result && $fetch_result->num_rows > 0) {
      $total_amount = $fetch_result->fetch_assoc()['total_amount'];

      // get user ip address
      $user_ip = getUserIP();

      // Insert the order with transaction details
      $insert_order = "INSERT INTO tbl_order (user_id, cart_id, payment_mode, total_amount, created_on, transaction_details, ip_address) VALUES ('$user_id', '$cart_id', '$paymentMethod', '$total_amount', '$current_time', '$transactionDetails', '$user_ip')";
      if ($conn->query($insert_order) === TRUE) {
        // Redirect to the success page after successful transaction
        header("Location: order_success.php");
        exit();
      } else {
        // Handle insert failure
        echo "Error inserting order: " . $conn->error;
      }
    } else {
      // Handle fetch failure
      echo "Error fetching total amount: " . $conn->error;
    }
  } else {
    // Handle cart status update failure
    echo "Error updating cart status: " . $conn->error;
  }
} else {
  echo "No transaction details available.";
}

function getUserIP()
{
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }

  // Convert IPv6 to IPv4 format if possible
  if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    $mapped_ipv4 = inet_ntop(inet_pton($ip));
    if (strpos($mapped_ipv4, '::ffff:') === 0) {
      $ip = substr($mapped_ipv4, 7); // Extract IPv4-mapped IPv6 address
    } elseif ($ip === '::1') {
      $ip = '127.0.0.1'; // Handle IPv6 loopback
    }
  }

  return $ip;
}

?>
