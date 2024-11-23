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
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Header Basic - Pages | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../public/assets/img/favicon/favicon.ico" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <!-- Icons. Uncomment required icon fonts -->
  <link rel="stylesheet" href="../public//assets/vendor/fonts/boxicons.css" />
  <!-- Add this to the <head> section of your HTML -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">


  <!-- Core CSS -->
  <link rel="stylesheet" href="../public/assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../public/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../public/assets/css/demo.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <!-- Page CSS -->
  <!-- Page -->
  <link rel="stylesheet" href="../public/assets/vendor/css/pages/page-auth.css" />
  <!-- Helpers -->
  <script src="../public/assets/vendor/js/helpers.js"></script>

  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
  <script src="../public/assets/js/config.js"></script>
  <style>
    ul li::marker {
      content: none;
      /* Removes the marker */
    }
  </style>
</head>

<body>

  <?php

  include '../../db_connection/connection.php'; // Include the database connection file

  // Check if user is logged in and retrieve the current user ID from session
  // Check if user is logged in and retrieve the current user ID from session
  if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Query to get the number of items in the cart for the current user
    $query = "SELECT COUNT(*) as cart_count FROM tbl_cart_item ci JOIN tbl_cart c ON ci.cart_id = c.cart_id WHERE ci.user_id = $user_id AND (c.cart_status < 2 OR c.cart_status IS NULL)";

    $result = mysqli_query($conn, $query);

    if ($result) {
      $row = mysqli_fetch_assoc($result);
      $cart_count = $row['cart_count'];
    } else {
      $cart_count = 0;
    }
  } else {
    // If user is not logged in, set cart count to 0
    $cart_count = 0;
  }


  ?>

  <header class="p-3 text-bg-dark bg-white">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="website_page.php" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
          <span class="icon-cart" style="font-size: 40px; display: inline-block; width: 40px; height: 40px; border-radius: 50%; background-color: #f8f9fa; display: flex; justify-content: center; align-items: center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart-fill" viewBox="0 0 16 16">
              <path d="M5.5 0a.5.5 0 0 1 .5.5V1h9a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H3.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h9V.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v.5h.5A2.5 2.5 0 0 1 16 3v10a2.5 2.5 0 0 1-2.5 2.5H2A2.5 2.5 0 0 1 0 13V3a2.5 2.5 0 0 1 2.5-2.5h.5V.5a.5.5 0 0 1 .5-.5h1z" />
            </svg>
          </span>
        </a>


        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="#" class="nav-link px-2 text-secondary">Home</a></li>
          <li><a href="#" class="nav-link px-2 text-secondary">Features</a></li>
          <li><a href="#" class="nav-link px-2 text-secondary">Pricing</a></li>
          <li><a href="#" class="nav-link px-2 text-secondary">FAQs</a></li>
          <li><a href="#" class="nav-link px-2 text-secondary">About</a></li>
        </ul>

        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
          <input type="search" class="form-control form-control-dark text-bg-dark" placeholder="Search..." aria-label="Search">
        </form>

        <div class="text-end">
          <?php

          // Check if the user is already logged in
          if (isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true) {
            // Display the user details dropdown if the user is logged in
            $username_input = $_SESSION['username'];
            $email_input = $_SESSION['email'];
          ?>
            <ul class="navbar-nav flex-row align-items-center ms-auto">
              <!-- User -->
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <svg class="w-px-40 h-auto rounded-circle" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
                      <!-- Circle -->
                      <circle cx="20" cy="20" r="18" fill="#f3f3f3" stroke="#ccc" stroke-width="2" />

                      <!-- User icon using foreignObject -->
                      <foreignObject x="8" y="8" width="24" height="24">
                        <i class="fa fa-user" style="font-size: 24px; color: #333;"></i>
                      </foreignObject>
                    </svg>
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="#">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <svg class="w-px-40 h-auto rounded-circle" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
                              <!-- Circle -->
                              <circle cx="20" cy="20" r="18" fill="#f3f3f3" stroke="#ccc" stroke-width="2" />

                              <!-- User icon using foreignObject -->
                              <foreignObject x="8" y="8" width="24" height="24">
                                <i class="fa fa-user" style="font-size: 24px; color: #333;"></i>
                              </foreignObject>
                            </svg>
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <span class="fw-semibold d-block"><?php echo htmlspecialchars($username_input); ?></span>
                          <small class="text-muted"><?php echo htmlspecialchars($email_input); ?></small>
                        </div>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="../logout.php">
                      <i class="bx bx-power-off me-2"></i>
                      <span class="align-middle">Log Out</span>
                    </a>
                  </li>
                </ul>
              </li>
              <!--/ User -->
            </ul>
          <?php
          } else {
            // Display the login and sign-up buttons if the user is not logged in
          ?>
            <div class="text-end">
              <a href="../login.php" type="button" class="btn btn-dark me-2">Login</a>
              <a href="../register.php" type="button" class="btn btn-primary">Sign-up</a>
            </div>
          <?php
          }
          ?>
        </div>

        <a href="add_to_cart.php" class="d-flex align-items-center ms-3 mb-2 mb-lg-0 text-dark text-decoration-none">
          <span>(<?php echo $cart_count; ?>)</span>
          <i class="fas fa-shopping-cart ms-1" style="font-size: 25px;"></i>
        </a>
      </div>
    </div>
  </header>
  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  <script src="../public/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../public/assets/vendor/libs/popper/popper.js"></script>
  <script src="../public/assets/vendor/js/bootstrap.js"></script>
  <script src="../public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

  <script src="../public/assets/vendor/js/menu.js"></script>
  <!-- endbuild -->

  <!-- Vendors JS -->

  <!-- Main JS -->
  <script src="../public/assets/js/main.js"></script>

  <!-- Page JS -->

  <!-- Place this tag in your head or just before your close body tag. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>