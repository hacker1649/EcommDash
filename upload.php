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

  <title>Upload - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>

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
</head>

<body>

  <?php

  session_start(); // Start the session to access stored data

  // Check if the user is already logged in
  if (isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true) {
    // Redirect to dashboard if user is already logged in
    header("Location: ./user/dashboard.php");
    exit;
  }

  // Check if the admin is logged in
  if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    // User is not logged in, redirect to the login page
    header("Location: login.php");
    exit;
  } else {
    $username_input = $_SESSION['username'];
    $email_input = $_SESSION['email'];
  }

  // validation code
  $filename = $file = "";
  $filenameErr = $fileErr = "";
  $success_message = ''; // Add a variable for success message
  $error_message = ''; // Add a variable for error message

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve file name 
    if (empty($_POST["filename"])) {
      $filenameErr = "File name is required";
    } else {
      $filename = $_POST["filename"];
      // Allow only letters, numbers, and white space
      if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $filename)) {
        $filenameErr = "Only letters, numbers, and white space are allowed";
      }
    }

    // Retrieve uploaded file
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
      $file = $_FILES['file'];
      // Check the file type (PDF only)
      $fileType = mime_content_type($file['tmp_name']);
      if ($fileType != "application/pdf") {
        $fileErr = "Only PDF files are allowed";
      }
      // Check the file size (must not exceed 2MB)
      $maxFileSize = 2 * 1024 * 1024;
      if ($file['size'] > $maxFileSize) {
        $fileErr = "File size should not exceed 2MB.";
      }
      // If no errors, save the file with the provided name
      if (empty($fileErr) && empty($filenameErr)) {
        $destDir = 'uploads/';
        // Check if the directory exists or not, if not, create it
        if (!is_dir($destDir)) {
          mkdir($destDir, 0777, true);
        }
        $filePath = $destDir . $filename . '.pdf';
        // Check if the file already exists
        if (file_exists($filePath)) {
          $timestamp = time();
          $newFilename = $timestamp . '_' . $filename . '.pdf';
          $filePath = $destDir . $newFilename;
        }
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
          $success_message = "Success: File uploaded successfully."; // Set success message
        } else {
          $error_message = "Error: Unable to upload file."; // Set error message
        }
      }
    } else {
      $fileErr = "File is required";
    }
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
                        <h3 class="mb-0">File Upload Form</h3>
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

                        <form class="mb-3" action="" method="POST" enctype="multipart/form-data">
                          <div class="mb-3">
                            <label for="filename" class="form-label">File Name <span class="error">*</span></label>
                            <input
                              type="text"
                              class="form-control"
                              id="filename"
                              name="filename"
                              placeholder="Enter file name"
                              value="<?php if (isset($filename) && !empty($filename)) echo $filename; ?>"
                              autofocus />
                            <span class="error">
                              <?php
                              if (isset($filenameErr) && !empty($filenameErr)) {
                                echo $filenameErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <label for="file" class="form-label">Upload File <span class="error">*</span></label>
                            <input
                              type="file"
                              class="form-control"
                              id="file"
                              name="file"
                              autofocus />
                            <span class="error">
                              <?php
                              if (isset($fileErr) && !empty($fileErr)) {
                                echo $fileErr;
                              }
                              ?>
                            </span>
                          </div>
                          <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Save</button>
                          </div>
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