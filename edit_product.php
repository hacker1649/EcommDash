<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================d
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

  <title>Edit User - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>

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
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .error {
      color: #ff0000;
    }

    .hide {
      display: none;
    }
  </style>
</head>

<body>

  <?php

  include "./db_connection/connection.php"; // Include the connection file

  session_start(); // Start session to store data temporarily 

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

  // Check and set user_id in session if passed in the URL
  if (isset($_GET['id'])) {
    $_SESSION['product_id'] = $_GET['id']; // Store product_id in session
  }

  // Retrieve user_id from session
  $product_id = $_SESSION['product_id'] ?? ""; // Use null coalescing operator to avoid undefined variable errors

  $query = "SELECT * FROM tbl_product AS p INNER JOIN tbl_category AS c ON p.category_id = c.category_id WHERE p.product_id = '$product_id'";
  $result = $conn->query($query);
  $product = $result->fetch_assoc();

  // Define variables and initialize with product data
  $productName = $product['product_name'];
  $productDesc = $product['product_desc'];
  $price = $product['product_price'];
  $category = $product['category_name'];

  // Validate form fields after submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["product_name"])) {
      $productNameErr = "Product name is required";
    } else {
      $productName = test_input($_POST["product_name"]);
    }

    if (empty($_POST["product_desc"])) {
      $productDescErr = "Product description is required";
    } else {
      $productDesc = test_input($_POST["product_desc"]);
    }

    if (empty($_POST["price"])) {
      $priceErr = "Product price is required";
    } else {
      $price = test_input($_POST["price"]);
      if (!preg_match("/^[0-9]+(\.[0-9]{1,2})?$/", $price)) {
        $priceErr = "Only numeric values with up to two decimal places are allowed";
      }
    }

    if (empty($_POST["category"])) {
      $categoryErr = "Category is required";
    } else {
      $category = test_input($_POST["category"]);
    }

    if (empty($productNameErr) && empty($productDescErr) && empty($priceErr) && empty($categoryErr)) {
      $current_time = time();

      //get the category id
      $category_id = "";
      $fetchQuery = "SELECT category_id FROM tbl_category WHERE category_name = '$category'";
      $result = $conn->query($fetchQuery);
      if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $category_id = $row['category_id'];
      }

      // Update product data
      $update_product = "UPDATE tbl_product SET product_name = '$productName', product_desc = '$productDesc', product_price = '$price', category_id = '$category_id', updated_on = '$current_time' WHERE product_id = '$product_id'";
      $conn->query($update_product);

      if ($conn->query($update_product) === TRUE) {
        $_SESSION['success'] = "Product record updated successfully...";
        // Redirect to the results page
        header("Location: product_list.php");
        exit(); // Ensure no further code is executed 
      } else {
        $error_message = "Error: " . $conn->error;
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
                <div class="d-flex align-items-end row">
                  <div class="col">
                    <div class="card mb-4 mx-auto">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Edit Product Form</h3>
                      </div>
                      <div class="card-body">

                        <!-- Display error message if validation fails -->
                        <?php if (!empty($error_message)): ?>
                          <div class="alert alert-danger alert-dismissible" role="alert">
                            <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>
                        <?php endif; ?>

                        <!-- Display success message if file upload is successful -->
                        <?php if (!empty($success_message)): ?>
                          <div class="alert alert-success alert-dismissible" role="alert">
                            <?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>
                        <?php endif; ?>

                        <!-- Form -->
                        <form id="dataForm" method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                          <div class="mb-3">
                            <label for="productName" class="form-label fw-bold">Product Name</label>
                            <span class="error">*</span>
                            <input type="text" class="form-control" id="productName" name="product_name" value="<?php if (isset($productName) && !empty($productName)) echo $productName; ?>" placeholder="Enter product name" />
                            <span class="error">
                              <?php
                              if (isset($productNameErr) && !empty($productNameErr)) {
                                echo $productNameErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="productDesc" class="form-label fw-bold">Product Description</label>
                            <span class="error">*</span>
                            <textarea class="form-control" id="productDesc" name="product_desc" rows="3" placeholder="Enter product description"><?php if (isset($productDesc) && !empty($productDesc)) echo $productDesc; ?></textarea>
                            <span class="error">
                              <?php
                              if (isset($productDescErr) && !empty($productDescErr)) {
                                echo $productDescErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="price" class="form-label fw-bold">Product Price</label>
                            <span class="error">*</span>
                            <input type="text" class="form-control" id="price" name="price" value="<?php if (isset($price) && !empty($price)) echo $price; ?>" placeholder="Enter product price" />
                            <span class="error">
                              <?php
                              if (isset($priceErr) && !empty($priceErr)) {
                                echo $priceErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="category" class="form-label fw-bold">Category</label>
                            <span class="error">*</span>
                            <select class="form-select" id="category" name="category">
                              <option value="" selected>Choose a Category</option>
                              <option value="Electronics" <?php if (isset($category) && $category == "Electronics") echo "selected"; ?>>Electronics</option>
                              <option value="Books" <?php if (isset($category) && $category == "Books") echo "selected"; ?>>Books</option>
                              <option value="Furniture" <?php if (isset($category) && $category == "Furniture") echo "selected"; ?>>Furniture</option>
                              <option value="Clothing" <?php if (isset($category) && $category == "Clothing") echo "selected"; ?>>Clothing</option>
                              <option value="Toys" <?php if (isset($category) && $category == "Toys") echo "selected"; ?>>Toys</option>
                            </select>
                            <span class="error">
                              <?php
                              if (isset($categoryErr) && !empty($categoryErr)) {
                                echo $categoryErr;
                              }
                              ?>
                            </span>
                          </div>
                          <button type="submit" name="submit" class="btn btn-primary">Save</button>
                        </form>
                      </div>
                    </div>
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