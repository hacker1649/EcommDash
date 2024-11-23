<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Order List - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="./public/assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <!-- Icons. Uncomment required icon fonts -->
  <link rel="stylesheet" href="./public/assets/vendor/fonts/boxicons.css" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="./public/assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="./public/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="./public/assets/css/demo.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="./public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <link rel="stylesheet" href="./public/assets/vendor/libs/apex-charts/apex-charts.css" />

  <!-- Page CSS -->

  <!-- Helpers -->
  <script src="./public/assets/vendor/js/helpers.js"></script>

  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
  <script src="./public/assets/js/config.js"></script>
  <style>
    .error {
      color: #ff0000;
    }

    .hide {
      display: none;
    }

    .profile-row {
      border: 1px solid #ddd;
      padding: 10px;
      margin-bottom: 5px;
      border-radius: 5px;
    }

    .cart-row {
      border: 3px solid #ddd;
      padding: 10px;
      margin-bottom: 5px;
      border-radius: 5px;
    }

    .truncate {
      max-width: 220px;
      /* Adjust as needed */
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    #myInput {
      margin-right: 10px;
      padding: 7px;
      border-radius: 3px;
      border: 1px solid #ddd;
      width: 100%;
    }

    .pagination {
      cursor: pointer;
    }

    .mar {
      padding-bottom: 30px;
    }
  </style>
</head>

<body>

  <?php

  include "./db_connection/connection.php"; // Include the connection file

  session_start(); // Start the session to access stored data

  // Check if the user is already logged in
  if (isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true) {
    // Redirect to dashboard if user is already logged in
    header("Location: ./user/dashboard.php");
    exit;
  }

  // Check if the user is logged in
  if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    // User is not logged in, redirect to the login page
    header("Location: login.php");
    exit;
  } else {
    $username_input = $_SESSION['username'];
    $email_input = $_SESSION['email'];
  }

  // Get the number of records per page from the dropdown or set a default
  $limit = 5; // Default to 5 if not set
  $page = isset($_POST['page']) ? (int)$_POST['page'] : 1; // Current page
  $offset = ($page - 1) * $limit; // Calculate offset

  // Initialize the search term
  $emailSearch = isset($_POST['email']) ? trim($_POST['email']) : '';

  // Build the base query
  $query =
    "SELECT c.cart_id, u.user_email, c.payment_mode, c.total_amount, c.created_on, c.cart_status
    FROM tbl_cart AS c 
    INNER JOIN tbl_user AS u ON c.user_id = u.user_id";

  // Add search conditions
  $conditions = [];
  if ($emailSearch) {
    $query .= " AND u.user_email LIKE '%$emailSearch%'";
  }

  // Calculate total number of rows after filtering
  $totalResult = $conn->query($query);
  $totalRows = $totalResult->num_rows;

  // Calculate total pages
  $totalPages = ceil($totalRows / $limit);

  // Calculate the starting row for the query
  $startRow = ($page - 1) * $limit;

  // Update query with LIMIT and OFFSET
  $query .= " LIMIT $startRow, $limit";

  // Execute the main query
  $result = $conn->query($query);

  ?>

  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->
      <?php include './layout/sidebar.php'; ?>
      <!-- / Menu -->

      <!-- Layout container -->
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
                <div class="card">

                  <?php if (isset($_SESSION['success'])): ?>
                    <!-- Success Message -->
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <?php
                      echo $_SESSION['success'];
                      unset($_SESSION['success']); // Unset the session message after displaying it
                      ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['error'])): ?>
                    <!-- Error Message -->
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <?php
                      echo $_SESSION['error'];
                      unset($_SESSION['error']); // Unset the session message after displaying it
                      ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php endif; ?>

                  <div class="d-flex align-items-end row">
                    <div class="col">
                      <div class="card-header mb-3 d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Order List</h3>
                      </div>
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <form style="width:35%" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                          <div class="d-flex justify-content-center align-items-center gap-2">
                            <input id="myInput" type="text" name="email" placeholder="Search by Email..." value="<?php echo isset($emailSearch) ? htmlspecialchars($emailSearch) : ''; ?>">
                            <button type="submit" class="btn btn-dark">Search</button>
                          </div>
                        </form>
                      </div>
                      <div class="card-body">
                        <div class="table-responsive mar">
                          <table class="table table-striped table-bordered">
                            <thead>
                              <tr>
                                <th>Order ID</th>
                                <th>User Email</th>
                                <th>Payment Mode</th>
                                <th>Total Amount</th>
                                <th>Created On</th>
                                <th>Status</th>
                                <th>Details</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                  <tr>
                                    <td><?php echo htmlspecialchars($row['cart_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['payment_mode'] ?? 'Not selected'); ?></td>
                                    <td><?php echo htmlspecialchars("$" . $row['total_amount']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d-m-Y | H:i:s', $row['created_on'])); ?></td>
                                    <td>
                                      <?php
                                      if ($row['cart_status'] == 3) {
                                      ?>
                                        <span class="bg-success text-white p-1 rounded-2">Completed</span>
                                      <?php
                                      } elseif ($row['cart_status'] == 2) {
                                      ?>
                                        <span class="bg-warning text-white p-1 rounded-2">Processing</span>
                                      <?php
                                      } elseif ($row['cart_status'] == 1) {
                                      ?>
                                        <span class="bg-danger text-white p-1 rounded-2">Pending</span>
                                      <?php
                                      }
                                      ?>
                                    </td>
                                    <td>
                                      <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo $row['cart_id']; ?>">View</button>
                                    </td>
                                  </tr>

                                  <!-- Details Modal -->
                                  <div class="modal fade" id="detailsModal<?php echo $row['cart_id']; ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?php echo $row['cart_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="detailsModalLabel<?php echo $row['cart_id']; ?>">Order Details</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                          <div class="row">
                                            <?php
                                            $cart_id = $row['cart_id']; // Use cart_id from the current order row
                                            $item_query =
                                              "SELECT 
                                                p.product_name, 
                                                p.product_price, 
                                                pi.folder, 
                                                c.total_amount, 
                                                ci.t_product_price, 
                                                ci.quantity 
                                              FROM tbl_cart_item AS ci 
                                              INNER JOIN tbl_cart AS c ON c.cart_id = ci.cart_id
                                              INNER JOIN tbl_product AS p ON ci.product_id = p.product_id
                                              INNER JOIN tbl_product_img AS pi ON p.product_id = pi.product_id
                                              WHERE ci.cart_id = $cart_id AND pi.img_priority = 'H'";

                                            $item_result = $conn->query($item_query);

                                            if ($item_result->num_rows > 0) {
                                              while ($item_row = $item_result->fetch_assoc()) {
                                            ?>
                                                <div class="container my-3">
                                                  <div class="row align-items-center border p-3 rounded shadow-sm">
                                                    <!-- Image Section -->
                                                    <div class="col-md-4 text-center">
                                                      <img src="<?php echo htmlspecialchars($item_row['folder'] ?? 'path/to/default-image.jpg'); ?>" alt="Product Image" class="img-fluid rounded">
                                                    </div>
                                                    <!-- Details Section -->
                                                    <div class="col-md-8">
                                                      <div class="row mb-1">
                                                        <div class="col-md-5 profile-row"><strong>Product Name</strong></div>
                                                        <div class="col-md-7 profile-row"><?php echo htmlspecialchars($item_row['product_name']); ?></div>
                                                      </div>
                                                      <div class="row mb-1">
                                                        <div class="col-md-5 profile-row"><strong>Product Price</strong></div>
                                                        <div class="col-md-7 profile-row">$<?php echo htmlspecialchars(number_format($item_row['product_price'], 2)); ?></div>
                                                      </div>
                                                      <div class="row mb-1">
                                                        <div class="col-md-5 profile-row"><strong>Quantity</strong></div>
                                                        <div class="col-md-7 profile-row"><?php echo htmlspecialchars($item_row['quantity']); ?></div>
                                                      </div>
                                                      <div class="row mb-1">
                                                        <div class="col-md-5 profile-row"><strong>Total Product Price</strong></div>
                                                        <div class="col-md-7 profile-row">$<?php echo htmlspecialchars(number_format($item_row['t_product_price'], 2)); ?></div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              <?php
                                              }
                                              ?>
                                          </div>
                                          <div class="float-end">
                                            <p class="fw-bold">Total Amount: $<?php echo number_format($row['total_amount'], 2); ?></p>
                                          </div>
                                        <?php
                                            } else {
                                              echo "<p class='text-muted'>No items found in the cart.</p>";
                                            }
                                        ?>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                <?php endwhile; ?>
                              <?php else: ?>
                                <tr>
                                  <td colspan='7'>No orders found</td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                        <!-- Pagination Links -->
                        <div class="d-flex justify-content-end align-items-center">
                          <nav aria-label="...">
                            <ul class="pagination">
                              <!-- Previous Button -->
                              <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a href="#" class="page-link" onclick="submitPaginationForm(<?php echo $page - 1; ?>)">Previous</a>
                              </li>

                              <!-- Page Numbers -->
                              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                  <a href="#" class="page-link" onclick="submitPaginationForm(<?php echo $i; ?>)"><?php echo $i; ?></a>
                                </li>
                              <?php endfor; ?>

                              <!-- Next Button -->
                              <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a href="#" class="page-link" onclick="submitPaginationForm(<?php echo $page + 1; ?>)">Next</a>
                              </li>
                            </ul>
                          </nav>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Hidden Form -->
          <form id="paginationForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($emailSearch); ?>">
            <input type="hidden" name="page" id="page">
          </form>

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
    function submitPaginationForm(page) {
      document.getElementById('page').value = page;
      document.getElementById('paginationForm').submit();
    }
  </script>

  <!-- build:js assets/vendor/js/core.js -->
  <script src="./public/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="./public/assets/vendor/libs/popper/popper.js"></script>
  <script src="./public/assets/vendor/js/bootstrap.js"></script>
  <script src="./public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

  <script src="./public/assets/vendor/js/menu.js"></script>
  <!-- endbuild -->

  <!-- Vendors JS -->
  <script src="./public/assets/vendor/libs/apex-charts/apexcharts.js"></script>

  <!-- Main JS -->
  <script src="./public/assets/js/main.js"></script>

  <!-- Page JS -->
  <script src="./public/assets/js/dashboards-analytics.js"></script>

  <!-- Place this tag in your head or just before your close body tag. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>