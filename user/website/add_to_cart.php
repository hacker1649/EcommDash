<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Add to Cart - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>
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

  // Mock condition to check if checkout form should be displayed
  $showCheckoutForm = false;

  // Disabling cart item controls
  $disableCartControls = false;

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
    WHERE ci.cart_id = $cart_id AND c.cart_status = 1 AND pi.img_priority = 'H'";

  $result_cart_items = $conn->query($sql_get_cart_items);

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['form_type'])) {
      $formType = $_POST['form_type'];
      if ($formType === 'checkoutButton') {
        if (isset($_POST['proceed_checkout'])) {
          $update_status = "UPDATE tbl_cart SET cart_status = 2 WHERE cart_id = $cart_id;";
          $conn->query($update_status);
          if ($conn->query($update_status) === TRUE) {
            // Update variables to display the checkout form and disable cart controls
            $showCheckoutForm = true;
            $disableCartControls = true;
          }
        }
      }
    }
  }

  $query = "SELECT * FROM tbl_user as u INNER JOIN tbl_user_profile as up ON u.user_id = up.user_id WHERE u.user_id = '$user_id'";
  $result = $conn->query($query);
  $user = $result->fetch_assoc();

  // Define variables and initialize with user data
  $firstName = $user['first_name'];
  $lastName = $user['last_name'];
  $email = $user['user_email'];
  $phone = $user['phone'];
  $address = $user['address'];

  // Validate form fields after submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['form_type'])) {
      $formType = $_POST['form_type'];
      if ($formType === 'checkoutForm') {

        if (!empty($_POST["phone"])) {
          $phone = test_input($_POST["phone"]);
        }

        if (!empty($_POST["address"])) {
          $address = test_input($_POST["address"]);
        }

        // Update user data
        $update_userData = "UPDATE tbl_user_profile SET phone = '$phone', address = '$address' WHERE user_id = '$user_id'";
        $conn->query($update_userData);

        if ($conn->query($update_userData) === TRUE) {
          header("Location: payment_page.php");
          exit();
        } else {
          echo "Error updating record: " . $conn->error;
        }
      }
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
                  <h4>Your Cart</h4>
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
                              <button type="submit" class="btn btn-danger" <?= $disableCartControls ? 'disabled' : ''; ?>>Remove</button>
                            </form>
                          </div>
                        </div>
                        <p><?php echo htmlspecialchars($item['product_desc']); ?></p>
                        <p>Price: $<?php echo htmlspecialchars($item['product_price']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                          <!-- Quantity controls -->
                          <div class="d-flex align-items-center">
                            <button onclick="updateQuantity(<?php echo $item['item_id']; ?>, -1)" class="btn btn-primary btn-sm" id="minusButton_<?php echo $item['item_id']; ?>" <?= $disableCartControls ? 'disabled' : ''; ?>>
                              <i class="fas fa-minus"></i>
                            </button>
                            <input type="text" id="quantity_<?php echo $item['item_id']; ?>" value="<?php echo htmlspecialchars($item['quantity']); ?>" readonly class="form-control fw-bold text-center mx-2" style="width: 50px;" <?= $disableCartControls ? 'disabled' : ''; ?>>
                            <button onclick="updateQuantity(<?php echo $item['item_id']; ?>, 1)" class="btn btn-primary btn-sm" id="plusButton_<?php echo $item['item_id']; ?>" <?= $disableCartControls ? 'disabled' : ''; ?>>
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
                  <div class="mt-5 ">
                    <form method="POST" action="">
                      <input type="hidden" name="form_type" value="checkoutButton">
                      <!-- Proceed to Checkout Button -->
                      <button type="submit" name="proceed_checkout" class="btn btn-dark mb-5" <?= $disableCartControls ? 'disabled' : ''; ?>>Proceed to Checkout</button>
                    </form>
                  </div>

                  <!-- Hidden Checkout Form -->
                  <div class="d-flex justify-content-center align-items-center">
                    <div id="checkoutForm" style="display: <?= $showCheckoutForm ? 'block' : 'none'; ?>; margin-top: 20px;" class="col-md-6 mb-5">
                      <div class="card shadow-lg">
                        <div class="card-header text-white">
                          <h4 class="m-0">Checkout Form</h4>
                        </div>
                        <div class="card-body">
                          <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateCheckoutForm();">
                            <input type="hidden" name="form_type" value="checkoutForm">

                            <!-- First Name and Last Name Fields -->
                            <div class="row">
                              <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label fw-bold">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" value="<?php echo $firstName; ?>" placeholder="Enter your first name" readonly />
                              </div>
                              <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label fw-bold">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" value="<?php echo $lastName; ?>" placeholder="Enter your last name" readonly />
                              </div>
                            </div>

                            <!-- Email Field -->
                            <div class="mb-3">
                              <label for="email" class="form-label fw-bold">Email</label>
                              <input type="text" class="form-control" id="email" name="useremail" value="<?php echo $email; ?>" readonly>
                            </div>

                            <!-- Address Field -->
                            <div class="mb-3">
                              <label for="address" class="form-label fw-bold">Address</label>
                              <input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>" placeholder="Enter your address">
                              <div id="addressError" class="text-danger"></div>
                            </div>

                            <!-- Phone Number Field -->
                            <div class="mb-5">
                              <label for="phone" class="form-label fw-bold">Phone</label>
                              <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" placeholder="Enter your phone number">
                              <div id="phoneError" class="text-danger"></div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success w-100">Continue</button>
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

  <script>
    function updateQuantity(itemId, change) {
      const quantityInput = document.getElementById(`quantity_${itemId}`);
      let currentQuantity = parseInt(quantityInput.value);

      // Update quantity
      const newQuantity = currentQuantity + change;
      quantityInput.value = newQuantity;
      window.location.href = `update_cart.php?item_id=${itemId}&quantity=${newQuantity}`;
    }
  </script>

  <script>
    // Function to validate address, phone, and payment method fields
    function validateCheckoutForm() {
      // Get field values
      const address = document.getElementById('address').value.trim();
      const phone = document.getElementById('phone').value.trim();

      // Get error containers
      const addressError = document.getElementById('addressError');
      const phoneError = document.getElementById('phoneError');

      // Clear previous error messages
      addressError.textContent = "";
      phoneError.textContent = "";

      let isValid = true;

      // Validate address
      if (address === "") {
        addressError.textContent = "Address cannot be empty.";
        addressError.style.color = "red";
        isValid = false;
      }

      // Validate phone
      if (phone === "") {
        phoneError.textContent = "Phone number cannot be empty.";
        phoneError.style.color = "red";
        isValid = false;
      }

      // Validate phone (must be exactly 11 digits)
      if (!/^\+?[0-9]{1,3}[-\s]?(\(?[0-9]{1,4}\)?[-\s]?)[0-9]{1,4}[-\s]?[0-9]{1,4}$/.test(phone)) {
        phoneError.textContent = "Invalid Phone number.";
        phoneError.style.color = "red";
        isValid = false;
      }

      return isValid;
    }

    // Add event listener for form submission
    document.querySelector('form').addEventListener('submit', function(event) {
      if (!validateCheckoutForm()) {
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