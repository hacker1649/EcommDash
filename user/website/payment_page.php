<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Proceed to Payment - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>
  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../../public/assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

  <!-- Icons -->
  <link rel="stylesheet" href="../../public/assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <!-- Core CSS -->
  <link rel="stylesheet" href="../../public/assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../../public/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../../public/assets/css/demo.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../../public/assets/vendor/libs/apex-charts/apex-charts.css" />

  <!-- Helpers -->
  <script src="../../public/assets/vendor/js/helpers.js"></script>

  <script src="../../public/assets/js/config.js"></script>

  <!-- including paypal javascript sdk -->
  <script src="https://www.paypal.com/sdk/js?client-id=AfF2yhhqglEvXPHhsVgly66Ma4u3CkFtpcp2G3ZS2OVhEdhU7A2e4oTwV-jh8Jw_2-ZWCTy5KyKEGnIV&currency=USD"></script>
  <style>
    @media (min-width: 1200px) {

      .layout-menu-fixed:not(.layout-menu-collapsed) .layout-page,
      .layout-menu-fixed-offcanvas:not(.layout-menu-collapsed) .layout-page {
        padding-left: 0rem;
      }
    }

    .cart-item {
      display: flex;
      align-items: center;
      border: 1px solid #ddd;
      padding: 15px;
      margin-bottom: 10px;
    }

    .cart-item img {
      max-width: 150px;
      margin-right: 20px;
    }

    .cart-item-details {
      flex: 1;
    }

    .quantity-input {
      width: 60px;
    }

    .update-btn,
    .remove-btn {
      cursor: pointer;
      color: #007bff;
      text-decoration: underline;
      margin-right: 10px;
    }

    .remove-btn {
      color: #ff0000;
    }
  </style>
</head>

<body>

  <?php

  include '../../db_connection/connection.php'; // Include your database connection

  session_start();

  if (!isset($_SESSION['userLoggedIn']) || $_SESSION['userLoggedIn'] !== true) {
    header("Location: ../login.php");
    exit;
  }

  // Retrieve user_id from session
  $user_id = $_SESSION['user_id'] ?? ""; // Use null coalescing operator to avoid undefined variable errors

  // Check if user has an active cart
  $sql_check_cart = "SELECT cart_id FROM tbl_cart WHERE user_id = $user_id AND cart_status = 1";
  $result_check_cart = $conn->query($sql_check_cart);

  if ($result_check_cart->num_rows > 0) {
    $cart_id = $result_check_cart->fetch_assoc()['cart_id'];
    $_SESSION['cart_id'] = $cart_id;
  }

  $cart_id = "";
  if (isset($_SESSION['cart_id'])) {
    $cart_id = $_SESSION['cart_id'];
  }
  // Fetch all items in the cart
  $sql_get_cart_items =
    "SELECT ci.item_id, ci.product_id, ci.product_price, ci.quantity, ci.t_product_price, p.product_name, p.product_desc, pi.folder 
    FROM tbl_cart_item AS ci
    JOIN tbl_product AS p ON ci.product_id = p.product_id
    JOIN tbl_product_img AS pi ON p.product_id = pi.product_id
    JOIN tbl_cart AS c ON ci.cart_id = c.cart_id
    WHERE ci.cart_id = $cart_id AND c.cart_status IN(1,2) AND pi.img_priority = 'H'";

  $result_cart_items = $conn->query($sql_get_cart_items);

  $paymentMethod = "";
  // Validate form fields after submission

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    $paymentMethod = test_input($_POST["payment_method"]);

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

        // Insert the order data in the tbl_order table
        $insert_order = "INSERT INTO tbl_order (user_id, cart_id, payment_mode, total_amount, created_on, ip_address) VALUES ('$user_id', '$cart_id', '$paymentMethod', '$total_amount', '$current_time', '$user_ip')";
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
  }

  // Function to sanitize data
  function test_input($data)
  {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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

  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <div class="layout-page">
        <!-- Navbar -->
        <?php include './layout/header.php'; ?>
        <!-- / Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
              <div class="col mb-4 order-0">
                <?php if ($result_cart_items && $result_cart_items->num_rows > 0): ?>
                  <h4>Review Your Order</h4>
                  <?php while ($item = $result_cart_items->fetch_assoc()): ?>
                    <div class="cart-item">
                      <img src="../../<?php echo htmlspecialchars($item['folder']); ?>" alt="...">
                      <div class="cart-item-details">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                          <div>
                            <h5 class="fw-bold m-0"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                          </div>
                          <div>
                            <form action="remove_cart.php" method="POST">
                              <input id="fetchProductID" type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                              <button type="submit" class="btn btn-danger" disabled>Remove</button>
                            </form>
                          </div>
                        </div>
                        <p><?php echo htmlspecialchars($item['product_desc']); ?></p>
                        <p>Price: $<?php echo htmlspecialchars($item['product_price']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                          <!-- Quantity controls -->
                          <div class="d-flex align-items-center">
                            <button class="btn btn-primary btn-sm" id="minusButton_<?php echo $item['item_id']; ?>" disabled>
                              <i class="fas fa-minus"></i>
                            </button>
                            <input type="text" id="quantity_<?php echo $item['item_id']; ?>" value="<?php echo htmlspecialchars($item['quantity']); ?>" readonly class="form-control fw-bold text-center mx-2" style="width: 50px;" disabled>
                            <button class="btn btn-primary btn-sm" id="plusButton_<?php echo $item['item_id']; ?>" disabled>
                              <i class="fas fa-plus"></i>
                            </button>
                          </div>
                          <div><span>Total Price: <strong>$<?php echo htmlspecialchars($item['t_product_price']); ?></strong></span></div>
                        </div>
                      </div>
                    </div>
                  <?php endwhile; ?>
                  <div class="d-flex justify-content-end align-items-center">
                    <?php
                    $fetch_amount = "SELECT total_amount FROM tbl_cart WHERE cart_id = $cart_id";
                    $fetch_result = $conn->query($fetch_amount);

                    if ($fetch_result && $fetch_result->num_rows > 0) {
                      $total_amount = $fetch_result->fetch_assoc();
                    ?>
                      <span id="grand-total">Total Amount: <strong>$<?php echo number_format($total_amount['total_amount'], 2); ?></strong></span>
                    <?php
                    }
                    ?>
                  </div>

                  <!-- Hidden Checkout Form -->
                  <div class="d-flex justify-content-center align-items-center">
                    <div id="paymentForm" style="display: block; margin-top: 20px;" class="col-md-6 mb-5">
                      <div class="card shadow-lg">
                        <div class="card-header text-white">
                          <h4 class="m-0">Payment Form</h4>
                        </div>
                        <div class="card-body">
                          <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validatePaymentForm();">
                            <!-- Payment Method Field -->
                            <div class="mb-3">
                              <label for="paymentMethod" class="form-label">Payment Method</label>
                              <select class="form-select" id="paymentMethod" name="payment_method" onchange="togglePayPalButton()">
                                <option value="" selected>Choose Payment Method</option>
                                <option value="cash_on_delivery" <?php echo ($paymentMethod == 'cash_on_delivery') ? 'selected' : ''; ?>>Cash on Delivery</option>
                                <option value="paypal" <?php echo ($paymentMethod == 'paypal') ? 'selected' : ''; ?>>PayPal</option>
                              </select>
                              <div id="paymentMethodError" class="text-danger"></div>
                            </div>

                            <!-- PayPal Button -->
                            <div id="paypal-button-container" class="mt-3 mb-3" style="display: none;"></div>

                            <!-- Confirm Modal -->
                            <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                              <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="confirmModalLabel">Confirmation</h5>
                                    <!-- Update the close button -->
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                    Are you sure you want to confirm this order?
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                    <button id="confirmOrder" name="confirm_order" type="submit" class="btn btn-success">Confirm</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>

                <?php else: ?>
                  <div class="d-flex flex-column justify-content-center align-items-center">
                    <p>Your cart is empty.</p>
                    <a href="website_page.php" class="btn btn-dark">Continue Shopping...</a>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <?php include './layout/footer.php'; ?>
        <!-- / Footer -->

        <div class="content-backdrop fade"></div>
      </div>
      <!-- Content wrapper -->
    </div>
    <!-- / Layout page -->
  </div>

  <!-- Overlay -->
  <div class="layout-overlay layout-menu-toggle"></div>
  </div>
  <!-- / Layout wrapper -->

  <!-- Core JS -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('paymentMethod').addEventListener('change', function() {
      if (this.value === 'cash_on_delivery') {
        // Trigger the modal
        let modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
      }
    });
  </script>

  <script>
    // Fetch the total amount dynamically from the span with ID `grand-total`
    let totalAmount = parseFloat(
      document.getElementById('grand-total')
      .innerText // Get the text inside the span
      .replace('Total Amount: $', '') // Remove the label and dollar sign
      .trim() // Remove any extra spaces
    );

    // Load the PayPal script dynamically
    paypal.Buttons({
      createOrder: function(data, actions) {
        // Create the order and specify the amount
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: totalAmount.toFixed(2) // Use dynamically fetched total amount
            }
          }]
        });
      },
      onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
          // Alert the user of a successful transaction
          alert('Transaction completed by ' + details.payer.name.given_name);

          // Store transaction details in session storage
          const transactionDetails = JSON.stringify(details);

          // Redirect to save_transaction.php with transaction details as a URL parameter
          window.location.href = 'save_transaction.php?transaction_details=' + transactionDetails;
        });
      },
      onCancel: function(data) {
        // Handle when the user cancels the payment
        alert('Transaction was cancelled.');
      },
      onError: function(err) {
        // Handle errors during the payment process
        console.error(err);
        alert('An error occurred during the transaction.');
      }
    }).render('#paypal-button-container'); // Render PayPal button in the specified container
  </script>

  <script>
    // Function to validate address, phone, and payment method fields
    function validatePaymentForm() {
      // Get field values
      const paymentMethod = document.getElementById('paymentMethod').value.trim();

      // Get error containers
      const paymentMethodError = document.getElementById('paymentMethodError');

      // Clear previous error messages
      paymentMethodError.textContent = "";

      let isValid = true;

      // Validate payment method
      if (paymentMethod === "") {
        paymentMethodError.textContent = "Please select a payment method.";
        paymentMethodError.style.color = "red";
        isValid = false;
      }

      return isValid;
    }

    // Function to toggle PayPal button visibility
    function togglePayPalButton() {
      const paymentMethod = document.getElementById("paymentMethod").value;
      const paypalButtonContainer = document.getElementById("paypal-button-container");

      if (paymentMethod === "paypal") {
        paypalButtonContainer.style.display = "block"; // Show PayPal button
      } else {
        paypalButtonContainer.style.display = "none"; // Hide PayPal button
      }
    }

    // Add event listener for form submission
    document.querySelector('form').addEventListener('submit', function(event) {
      if (!validatePaymentForm()) {
        event.preventDefault(); // Prevent form submission if validation fails
      }
    });
  </script>

  <script src="../../public/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../../public/assets/vendor/libs/popper/popper.js"></script>
  <script src="../../public/assets/vendor/js/bootstrap.js"></script>
  <script src="../../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../../public/assets/vendor/js/menu.js"></script>

  <!-- Vendors JS -->
  <script src="../../public/assets/vendor/libs/apex-charts/apexcharts.js"></script>

  <!-- Main JS -->
  <script src="../../public/assets/js/main.js"></script>

  <!-- Page JS -->
  <script src="../../public/assets/js/dashboards-analytics.js"></script>

  <!-- Place this tag in your head or just before your close body tag. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>

</body>

</html>