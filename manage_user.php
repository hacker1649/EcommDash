<?php
include "./db_connection/connection.php"; // Include the connection file
?>

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

  <title>Manage User - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>

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

    .truncate {
      max-width: 197px;
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
  $firstNameSearch = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
  $lastNameSearch = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
  $emailSearch = isset($_POST['email']) ? trim($_POST['email']) : '';

  // Build the base query with SQL_CALC_FOUND_ROWS
  $query =
    "SELECT fu.file_path, u.user_id, u.user_email, u.user_password, u.created_on, u.user_status, up.file_name, up.first_name, up.last_name, up.age, up.gender, up.phone, up.profession, up.degree, up.major, up.institute, up.address, up.country, up.state, up.city, up.comments 
    FROM tbl_user AS u 
    INNER JOIN tbl_user_profile AS up 
    ON u.user_id = up.user_id
    LEFT JOIN tbl_file_upload AS fu
    ON u.user_id = fu.user_id AND fu.file_status = 1 
    WHERE u.user_status = 1";

  // Add search conditions
  $conditions = [];
  if ($firstNameSearch) {
    $query .= " AND up.first_name LIKE '%$firstNameSearch%'";
  }
  if ($lastNameSearch) {
    $query .= " AND up.last_name LIKE '%$lastNameSearch%'";
  }
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


  $filename = '';
  $filenameErr = '';
  $file = "";
  $fileErr = '';
  // Check if the form is submitted
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['form_type'])) {
      $formType = $_POST['form_type'];
      if ($formType === 'fileUploadForm') {
        // Retrieve user_id from the submitted form
        $id = $_POST['user_id'];

        // Retrieve file name 
        if (empty($_POST["filename"])) {
          $filenameErr = "File name is required";
          $_SESSION['val_error'] = "File name is required";
        } else {
          $filename = $_POST["filename"];
          // Allow only letters, numbers, and white space
          if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $filename)) {
            $filenameErr = "Only letters, numbers, and white space are allowed";
            $_SESSION['val_error'] = "Only letters, numbers, and white space are allowed";
          }
        }

        // Retrieve uploaded file
        if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
          $file = $_FILES['file'];

          // Define allowed MIME types
          $allowedTypes = ["application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document"];
          // Check the file type (PDF or Word only)
          $fileType = mime_content_type($file['tmp_name']);
          if (!in_array($fileType, $allowedTypes)) {
            $fileErr = "Only PDF and Word files are allowed";
            $_SESSION['val_error'] = "Only PDF and Word files are allowed";
          }

          // Check the file type (PDF or Word only)
          $fileType = mime_content_type($file['tmp_name']);
          if ($fileType != "application/pdf" && $fileType != "application/msword" && $fileType != "application/vnd.openxmlformats-officedocument.wordprocessingml.document") {
            $fileErr = "Only PDF and Word files are allowed";
            $_SESSION['val_error'] = "Only PDF and Word files are allowed";
          }

          // Check the file size (must not exceed 2MB)
          $maxFileSize = 2 * 1024 * 1024;
          if ($file['size'] > $maxFileSize) {
            $fileErr = "File size should not exceed 2MB";
            $_SESSION['val_error'] = "File size should not exceed 2MB";
          }

          // If no errors, save the file with the provided name
          if (empty($fileErr) && empty($filenameErr)) {
            $destDir = 'uploads/' . $id . '/';
            // Check if the directory exists or not, if not, create it
            if (!is_dir($destDir)) {
              mkdir($destDir, 0777, true);
            }

            // Determine the file extension based on MIME type
            if ($fileType === 'application/pdf') {
              $fileExtension = '.pdf';
            } elseif ($fileType === 'application/msword') {
              $fileExtension = '.doc';
            } elseif ($fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
              $fileExtension = '.docx';
            } else {
              $fileExtension = '';
            }

            $fullFileName = $filename . $fileExtension;
            $filePath = $destDir . $fullFileName;

            // Get the latest uploaded file details
            $query = "SELECT * FROM tbl_file_upload WHERE user_id = $id ORDER BY created_on DESC LIMIT 1";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
              // Fetch the latest file details
              $lastUploadedFile = $result->fetch_assoc();

              // Access file details
              $lastfileName = $lastUploadedFile['file_name'];
              $lastfilePath = $lastUploadedFile['file_path'];

              // Prepare to rename the file with a timestamp
              $current_time = time();
              $existingFileName = $current_time . '_' . pathinfo($lastfileName, PATHINFO_FILENAME) . '.' . pathinfo($lastfileName, PATHINFO_EXTENSION);
              $existingFilePath = $destDir . $existingFileName;

              // Rename the existing file on the server
              if (rename($lastfilePath, $existingFilePath)) {
                // Get the file type and size from the uploaded file
                $fileType = $_FILES['file']['type'];
                $fileSize = $_FILES['file']['size'];

                // Update the file path and other details in the database
                $updateQuery = "UPDATE tbl_file_upload SET file_name = '$existingFileName', file_path = '$existingFilePath', file_type = '$fileType', file_size = '$fileSize', updated_on = $current_time WHERE user_id = $id AND file_name = '$lastfileName'";

                if ($conn->query($updateQuery) === TRUE) {
                  $_SESSION['u_success'] = "Success! Your previous file has been successfully renamed, saved in the designated folder, and updated in the database.";
                  header("Location: manage_user.php");
                } else {
                  $_SESSION['error'] = "Failed to update file details in the database: " . $conn->error;
                }
              } else {
                $_SESSION['error'] = "Failed to rename the existing file.";
              }
            }

            if (move_uploaded_file($file['tmp_name'], $filePath)) {

              // Prepare to insert file details into the database
              $user_id = $id; // Replace with the actual user ID
              $created_on = time(); // Current timestamp for created_on
              $file_name = basename($filename) . "." . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
              $file_type = $_FILES['file']['type'];
              $file_size = $_FILES['file']['size'];
              $file_path = $filePath; // Path where the file is stored
              $file_status = 1; // Assuming 1 means active; adjust as needed

              // Update the file status of existing records to 0
              $updateSql = "UPDATE tbl_file_upload SET file_status = 0 WHERE user_id = $user_id AND file_status = 1";

              if ($conn->query($updateSql) === TRUE) {
                $sql = "INSERT INTO tbl_file_upload (user_id, file_name, file_type, file_size, file_path, created_on, file_status) 
                VALUES ($user_id, '$file_name', '$file_type', '$file_size', '$file_path', $created_on, $file_status)";
              }

              // Execute the insert query
              if ($conn->query($sql) === TRUE) {
                $_SESSION['success'] = "Success! Your file has been successfully uploaded and saved in the designated folder, as well as recorded in the database.";
                header("Location: manage_user.php");
              } else {
                $_SESSION['error'] = "Failed to save file details in the database: " . $conn->error;
              }
            }
          } else {
            $_SESSION['error'] = "Error: Unable to upload file."; // Set error message
          }
        } else {
          $fileErr = "File is required";
          $_SESSION['error'] = "File is required";
        }
      }
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
                <div class="card">

                  <?php if (isset($_SESSION['u_success'])): ?>
                    <!-- Success Message -->
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <?php
                      echo $_SESSION['u_success'];
                      unset($_SESSION['u_success']); // Unset the session message after displaying it
                      ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php endif; ?>

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

                  <?php if (isset($_SESSION['val_error'])): ?>
                    <!-- Error Message -->
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <?php
                      echo $_SESSION['val_error'];
                      unset($_SESSION['val_error']); // Unset the session message after displaying it
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
                        <h3 class="mb-0">Users Info</h3>
                        <a href="./add_user.php"><button type="button" class="btn btn-primary">Add User</button></a>
                      </div>
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                          <div class="d-flex justify-content-center align-items-center gap-2">
                            <input id="myInput" type="text" name="first_name" placeholder="Search by First Name..." value="<?php echo isset($firstNameSearch) ? htmlspecialchars($firstNameSearch) : ''; ?>">
                            <input id="myInput" type="text" name="last_name" placeholder="Search by Last Name..." value="<?php echo isset($lastNameSearch) ? htmlspecialchars($lastNameSearch) : ''; ?>">
                            <input id="myInput" type="text" name="email" placeholder="Search by Email..." value="<?php echo isset($emailSearch) ? htmlspecialchars($emailSearch) : ''; ?>">
                            <button type="submit" class="btn btn-success">Search</button>
                          </div>
                        </form>
                      </div>
                      <div class="card-body">
                        <div class="table-responsive mar">
                          <table class="table table-striped table-bordered">
                            <thead>
                              <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Created On</th>
                                <th>Detail</th>
                                <th>Action</th>
                                <th>Download</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                  <tr>
                                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                                    <td class="truncate"><?php echo htmlspecialchars($row['user_email']); ?></td>
                                    <td class="truncate"><?php echo htmlspecialchars($row['user_password']); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_on']); ?></td>
                                    <td>
                                      <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo $row['user_id']; ?>">View</button>
                                    </td>
                                    <td>
                                      <a href="./edit_user.php?id=<?php echo $row['user_id']; ?>"><button type="button" class="btn btn-warning">Edit</button></a>
                                      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $row['user_id']; ?>">Delete</button>
                                      <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#fileUploadModal<?php echo $row['user_id']; ?>">Upload</button>
                                    </td>
                                    <td>
                                      <?php if (!empty($row['file_path'])): ?>
                                        <a href="<?php echo htmlspecialchars($row['file_path']); ?>" class="btn btn-dark" download>Download</a>
                                      <?php else: ?>
                                        <span>No file exists</span>
                                      <?php endif; ?>
                                    </td>
                                  </tr>

                                  <!-- Details Modal -->
                                  <div class="modal fade" id="detailsModal<?php echo $row['user_id']; ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?php echo $row['user_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="detailsModalLabel<?php echo $row['user_id']; ?>">User Profile</h5>
                                          <!-- Update the close button -->
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="container modal-body">
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Profile Photo Name</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['file_name']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>First Name</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['first_name']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Last Name</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['last_name']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Age</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['age']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Gender</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['gender']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Email</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['user_email']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Password</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['user_password']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Phone</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['phone']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Profession</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['profession']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Degree</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['degree']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Major</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['major']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Institute</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['institute']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Address</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['address']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Country</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['country']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>State</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['state']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>City</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['city']); ?></div>
                                          </div>
                                          <div class="row">
                                            <div class="col-md-4 profile-row"><strong>Comments</strong></div>
                                            <div class="col-md-8 profile-row"><?php echo htmlspecialchars($row['comments']); ?></div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                  <!-- Delete Modal -->
                                  <div class="modal fade" id="deleteModal<?php echo $row['user_id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $row['user_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="deleteModalLabel<?php echo $row['user_id']; ?>">Warning</h5>
                                          <!-- Update the close button -->
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                          Are you sure you want to delete this record? This action cannot be undone.
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                          <a id="confirmDelete" href="./delete_user.php?id=<?php echo $row['user_id']; ?>" class="btn btn-danger">Delete</a>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                  <!-- File Upload Modal -->
                                  <div class="modal fade" id="fileUploadModal<?php echo $row['user_id']; ?>" tabindex="-1" aria-labelledby="fileUploadModalLabel<?php echo $row['user_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="fileUploadModalLabel<?php echo $row['user_id']; ?>">File Upload Form</h5>
                                          <!-- Update the close button -->
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                          <form action="" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="form_type" value="fileUploadForm">
                                            <!-- Hidden field to pass user_id -->
                                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">

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
                                            </div>
                                            <div class="mb-5">
                                              <label for="file" class="form-label">Upload File <span class="error">*</span></label>
                                              <input
                                                type="file"
                                                class="form-control"
                                                id="file"
                                                name="file"
                                                autofocus />
                                            </div>
                                            <div class="mb-3">
                                              <button class="btn btn-primary d-grid float-end" type="submit">Upload</button>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                <?php endwhile; ?>
                              <?php else: ?>
                                <tr>
                                  <td colspan='8'>No records found</td>
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
            <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($firstNameSearch); ?>">
            <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($lastNameSearch); ?>">
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