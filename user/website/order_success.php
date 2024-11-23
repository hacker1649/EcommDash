<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>Order Success - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>
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

    .success-container {
      text-align: center;
      padding: 50px 20px;
    }

    .success-container h1 {
      color: #4caf50;
    }

    .order-details {
      margin: 20px 0;
    }

    .order-details p {
      margin: 5px 0;
    }

    .btn-group {
      margin-top: 20px;
    }

    .btn-group a {
      margin: 5px;
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

  // Retrieve user_id and cart_id from session
  $user_id = $_SESSION['user_id'] ?? ""; // Use null coalescing operator to avoid undefined variable errors
  $cart_id = $_SESSION['cart_id'] ?? ""; // Use null coalescing operator to avoid undefined variable errors

  // Fetch cart details for the specific cart_id
  $fetch_order =
    "SELECT c.cart_id, c.total_amount, c.payment_mode, ci.product_id, ci.quantity, p.product_name, p.product_price
  FROM tbl_cart AS c
  JOIN tbl_cart_item ci ON c.cart_id = ci.cart_id
  JOIN tbl_product p ON ci.product_id = p.product_id
  WHERE c.user_id = $user_id AND c.cart_id = $cart_id AND c.cart_status = 3"; // Status 3 indicates completed orders

  $order_items = $conn->query($fetch_order);

  // Check if there are any order items for this cart
  if ($order_items->num_rows === 0) {
    echo "<p>No orders found for the specified cart.</p>";
    exit; 
  }

  $total_amount = 0;
  $paymentMethod = "";

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
                <div class="success-container">
                  <h1>Order Placed Successfully!</h1>
                  <p>Thank you for shopping with us. Your order has been confirmed!</p>

                  <h4>Your Order Details:</h4>

                  <div class="d-flex justify-content-center align-items-center">
                    <table class="table table-bordered table-hover w-50">
                      <thead>
                        <tr>
                          <th>Product Name</th>
                          <th>Quantity</th>
                          <th>Price</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php while ($item = $order_items->fetch_assoc()):
                          $total_amount += $item['product_price'] * $item['quantity'];
                          $paymentMethod = $item['payment_mode'];
                        ?>
                          <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                          </tr>
                        <?php endwhile; ?>
                      </tbody>
                    </table>
                  </div>

                  <div class="order-details">
                    <p><strong>Total Amount: </strong>$<?php echo number_format($total_amount, 2); ?></p>
                    <p><strong>Payment Method: </strong><?php echo htmlspecialchars($paymentMethod); ?></p>
                  </div>

                  <div class="btn-group">
                    <a href="website_page.php" class="btn btn-dark">Go Back to Shopping</a>
                  </div>

                  <p class="mt-4">If you have any questions, please contact our <a href="#">support team</a>.</p>
                </div>
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