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

  <title>Product List - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>

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
  $productNameSearch = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
  $categoryNameSearch = isset($_POST['category_name']) ? trim($_POST['category_name']) : '';
  $priceSearch = isset($_POST['p_price']) ? trim($_POST['p_price']) : '';

  // Build the base query with SQL_CALC_FOUND_ROWS
  $query =
    "SELECT c.category_name, p.product_id, p.product_name, p.product_price, p.product_desc, p.created_on
    FROM tbl_category AS c 
    INNER JOIN tbl_product AS p 
    ON c.category_id = p.category_id
    WHERE p.product_status = 1";

  // Add search conditions
  $conditions = [];
  if ($productNameSearch) {
    $query .= " AND p.product_name LIKE '%$productNameSearch%'";
  }
  if ($categoryNameSearch) {
    $query .= " AND c.category_name LIKE '%$categoryNameSearch%'";
  }
  if ($priceSearch) {
    $query .= " AND p.product_price LIKE '%$priceSearch%'";
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

  // photo uplodation code ---
  // Check if the form is submitted
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['form_type'])) {
      $formType = $_POST['form_type'];
      if ($formType === 'fileUploadForm') {
        // Retrieve product_id from the submitted form
        $id = $_POST['product_id'];

        // Create a folder for product images
        $targetDir = "uploads/products/" . $id . "/";
        // Check if the directory exists or not, if not, create it
        if (!is_dir($targetDir)) {
          mkdir($targetDir, 0777, true);
        }

        // Query to fetch existing images from the database
        $existingImages = "";
        $sql = "SELECT COUNT(*) AS imageCount FROM tbl_product_img WHERE product_id='$id'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
          // Fetch the count from the result
          $row = $result->fetch_assoc();
          $existingImages = $row['imageCount'];
        }
        $maxImages = 5;

        $remainingImages = $maxImages - $existingImages;

        // Get the number of images selected for upload
        $selectedImages = count($_FILES['images']['name']);

        // Check if the selected images exceed the remaining space
        if ($selectedImages > $remainingImages && $remainingImages != 0) {
          $_SESSION['error'] = "You can only upload " . $remainingImages . " more image(s). Please select fewer images.";
          header("Location: product_list.php");
          exit;
        } else if ($remainingImages == 0) {
          $_SESSION['error'] = "Upload space is full. You cannot upload any more images.";
          header("Location: product_list.php");
          exit;
        }

        $imageCount = 0;
        $allowedMimeTypes = ['image/jpeg', 'image/png'];

        // Process each file in the upload array
        foreach ($_FILES['images']['name'] as $key => $name) {
          if ($_FILES['images']['error'][$key] == UPLOAD_ERR_OK) {
            $tmpName = $_FILES['images']['tmp_name'][$key];
            $fileName = basename($name);
            $targetFilePath = $targetDir . $fileName;

            //check the format of the file upload (can be only images)
            $imageMimeType = mime_content_type($tmpName);
            if (!in_array($imageMimeType, $allowedMimeTypes)) {
              $_SESSION['error'] = "Only image files (JPEG, PNG) are allowed.";
              header("Location: product_list.php");
              exit;
            }

            // Move the uploaded file to the target directory
            if (move_uploaded_file($tmpName, $targetFilePath)) {
              $current_time = time();
              $sql = "INSERT INTO tbl_product_img (file_name, folder, product_id, created_on) VALUES ('$fileName', '$targetFilePath', '$id', '$current_time')";
              if ($conn->query($sql) === TRUE) {
                $imageCount++;
              }
            }
          } else {
            $_SESSION['error'] = "An error occurred while uploading the file.";
            header("Location: product_list.php");
            exit;
          }
        }

        // After processing the images
        if ($imageCount > 0) {
          $_SESSION['success'] = "Images uploaded successfully!";
        } else {
          $_SESSION['error'] = "No valid images were uploaded.";
        }

        header("Location: product_list.php");
        exit;
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
                        <h3 class="mb-0">Product List</h3>
                        <a href="./add_product.php"><button type="button" class="btn btn-primary">Add Product</button></a>
                      </div>
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                          <div class="d-flex justify-content-center align-items-center gap-2">
                            <input id="myInput" type="text" name="product_name" placeholder="Search by Product Name..." value="<?php echo isset($productNameSearch) ? htmlspecialchars($productNameSearch) : ''; ?>">
                            <input id="myInput" type="text" name="category_name" placeholder="Search by Category Name..." value="<?php echo isset($categoryNameSearch) ? htmlspecialchars($categoryNameSearch) : ''; ?>">
                            <input id="myInput" type="text" name="p_price" placeholder="Search by Price..." value="<?php echo isset($priceSearch) ? htmlspecialchars($priceSearch) : ''; ?>">
                            <button type="submit" class="btn btn-success">Search</button>
                          </div>
                        </form>
                      </div>
                      <div class="card-body">
                        <div class="table-responsive mar">
                          <table class="table table-striped table-bordered">
                            <thead>
                              <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Uploads</th>
                                <th>Created On</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                  <?php
                                    // Query to fetch the count of existing images for each product
                                    $product_id = $row['product_id'];
                                    $imgCountQuery = "SELECT COUNT(*) AS imageCount FROM tbl_product_img WHERE product_id='$product_id'";
                                    $imgCountResult = $conn->query($imgCountQuery);
                                    $existingImages = 0;
                                    if ($imgCountResult && $imgCountResult->num_rows > 0) {
                                      $imgRow = $imgCountResult->fetch_assoc();
                                      $existingImages = $imgRow['imageCount'];
                                    }
                                  ?>
                                  <tr>
                                  <tr>
                                    <td class="truncate"><?php echo htmlspecialchars($row['product_name']); ?></td>
                                    <td class="truncate"><?php echo htmlspecialchars($row['product_desc']); ?></td>
                                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['product_price']); ?></td>
                                    <td><?php echo $existingImages; ?> image(s)</td>
                                    <td><?php echo htmlspecialchars(date('d-m-Y | H:i:s', $row['created_on'])); ?></td>
                                    <td>
                                      <a href="./edit_product.php?id=<?php echo $row['product_id']; ?>"><button type="button" class="btn btn-warning">Update</button></a>
                                      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $row['product_id']; ?>">Block</button>
                                      <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#fileUploadModal<?php echo $row['product_id']; ?>">Photo Upload</button>
                                    </td>
                                  </tr>

                                  <!-- Delete Modal -->
                                  <div class="modal fade" id="deleteModal<?php echo $row['product_id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $row['product_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="deleteModalLabel<?php echo $row['product_id']; ?>">Warning</h5>
                                          <!-- Update the close button -->
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                          Are you sure you want to block this product? This action cannot be undone.
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                          <a id="confirmDelete" href="./block_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-danger">Block</a>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                  <!-- File Upload Modal -->
                                  <div class="modal fade" id="fileUploadModal<?php echo $row['product_id']; ?>" tabindex="-1" aria-labelledby="fileUploadModalLabel<?php echo $row['product_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="fileUploadModalLabel<?php echo $row['product_id']; ?>">File Upload Form</h5>
                                          <!-- Update the close button -->
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                          <form action="" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="form_type" value="fileUploadForm">
                                            <!-- Hidden field to pass user_id -->
                                            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                            <div class="mb-5">
                                              <label for="images" class="form-label">Upload Images <span class="error">*</span></label>
                                              <input
                                                type="file"
                                                name="images[]"
                                                class="form-control"
                                                multiple
                                                accept="image/*" />
                                              <small class="form-text text-muted">You can upload up to 5 images.</small>
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
            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($productNameSearch); ?>">
            <input type="hidden" name="category_name" value="<?php echo htmlspecialchars($categoryNameSearch); ?>">
            <input type="hidden" name="p_price" value="<?php echo htmlspecialchars($priceSearch); ?>">
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