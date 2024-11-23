<!DOCTYPE html>

<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>User Website - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>
  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../../public/assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

  <!-- Icons -->
  <link rel="stylesheet" href="../../public/assets/vendor/fonts/boxicons.css" />

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

    .truncate {
      max-height: 200px;
      /* Adjust as needed */
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    .card-body {
      padding-left: 15px;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
      background-color: lightgrey;
      opacity: 0.3;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      transition: background-color 0.3s ease;
    }

    .carousel-control-prev-icon:hover,
    .carousel-control-next-icon:hover {
      background-color: lightgrey;
      opacity: 0.3;

    }

    .card {
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }
  </style>
</head>

<body>

  <?php
  include "../../db_connection/connection.php"; // Include the connection file
  session_start();

  // Fetch categories with their image paths
  $sql_categories = "SELECT category_id, category_name, img_path FROM tbl_category";
  $result_categories = $conn->query($sql_categories);

  $categories = [];
  if ($result_categories && $result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {
      $categories[] = $row;
    }
  }

  // Fetch product images with product names
  $sql_images =
  "SELECT pi.file_name, pi.folder, p.category_id, p.product_id, p.product_name, p.product_price, p.product_desc, c.category_name
  FROM tbl_category AS c
  INNER JOIN tbl_product AS p ON c.category_id = p.category_id
  INNER JOIN tbl_product_img AS pi ON p.product_id = pi.product_id
  WHERE p.product_status = 1 AND pi.img_priority = 'H'";
  $result_images = $conn->query($sql_images);

  $product_images = [];
  if ($result_images && $result_images->num_rows > 0) {
    while ($row = $result_images->fetch_assoc()) {
      $product_images[] = $row;
    }
  } else {
    echo "No product images found";
  }

  //fetching hot (high priority) products from database
  $sql_hot_products =
  "SELECT pi.file_name, pi.folder, p.product_id, p.product_name, p.product_price, p.product_desc, p.popularity
  FROM tbl_product_img AS pi
  JOIN tbl_product AS p ON pi.product_id = p.product_id
  WHERE p.product_status = 1 AND p.popularity = 1 AND pi.img_priority = 'H'";
  $result_hot_products = $conn->query($sql_hot_products);

  $hot_products = [];
  if ($result_hot_products->num_rows > 0) {
    while ($row = $result_hot_products->fetch_assoc()) {
      $hot_products[] = $row;
    }
  } else {
    echo "No featured products found...";
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

                <!-- Carousel Section -->
                <div id="carouselExample" class="carousel slide" data-bs-ride="carousel" style="width: 100%; height: 600px; margin-bottom: 50px;">
                  <div class="carousel-inner">
                    <?php
                    $activeClass = "active";
                    foreach ($categories as $category) {
                      $imagePath = $category['img_path'];
                      $categoryName = htmlspecialchars($category['category_name']);
                      $categoryId = $category['category_id']; // Store the category ID
                    ?>
                      <div class="carousel-item <?php echo $activeClass; ?>">
                        <a href="#category-<?php echo $categoryId; ?>">
                          <img src="../../<?php echo $imagePath; ?>" class="d-block w-100" alt="..." style="height: 600px; object-fit: cover;">
                          <div class="carousel-caption d-none d-md-block">
                            <h5><?php echo $categoryName; ?></h5>
                          </div>
                        </a>
                      </div>
                    <?php
                      $activeClass = ""; // Clear the active class after the first item
                    }
                    ?>
                  </div>

                  <!-- Pagination bars -->
                  <div class="carousel-indicators">
                    <?php
                    $i = 0;
                    foreach ($categories as $category) {
                    ?>
                      <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="<?php echo $i; ?>" class="<?php echo ($i == 0 ? 'active' : ''); ?>" aria-current="true" aria-label="Slide <?php echo $i + 1; ?>"></button>
                    <?php
                      $i++;
                    }
                    ?>
                  </div>

                  <!-- Carousel controls -->
                  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                  </button>
                </div>

                <!-- code to display all the featured products -->
                <div class="carousel-name text-white py-3 pe-4 rounded">
                  <h5 class="m-0">Featured Products</h5>
                </div>
                <div id="hotProductsCarousel" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-inner">
                    <?php
                    $activeClass = "active";
                    $hotProductCount = count($hot_products);
                    for ($i = 0; $i < $hotProductCount; $i += 3): ?>
                      <div class="carousel-item <?php echo ($activeClass ? 'active' : ''); ?>">
                        <div class="d-flex justify-content-between">
                          <?php
                          for ($j = 0; $j < 3; $j++) {
                            if (isset($hot_products[$i + $j])) {
                              $product = $hot_products[$i + $j];
                              $productName = $product['product_name'];
                              $productId = $product['product_id'];
                              $imagePath = $product['folder'];
                              $price = $product['product_price'];
                              $description = $product['product_desc'];
                          ?>
                              <div class="card" style="width: 30%">
                                <!-- Badge for Featured/Hot Product -->
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">Hot</span>
                                <img src="../../<?php echo $imagePath; ?>" class="card-img-top" alt="..." style="height: 300px; object-fit: cover;">
                                <div class="card-body">
                                  <h5 class="card-title text-black"><?php echo $productName; ?></h5>
                                  <p class="card-text text-muted truncate"><?php echo $description; ?></p> <!-- Product description with limited height -->
                                  <h6 class="text-black">Price: $ <?php echo number_format($price, 2); ?></h6> <!-- Display the price -->
                                  <a href="product_details.php?product_id=<?php echo $productId; ?>" class="btn btn-primary btn-show-details w-100"">Show Details</a>
                                </div>
                              </div>
                          <?php
                            }
                          }
                          ?>
                        </div>
                      </div>
                    <?php
                      $activeClass = "";
                    endfor;
                    ?>
                  </div>
                  <button class=" carousel-control-prev" type="button" data-bs-target="#hotProductsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#hotProductsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                  </button>
                </div>

                <!-- code to display all the products grouped by their category -->
                <div class="carousel-container">
                  <?php
                  // Loop through categories for small carousels
                  $category_ids = [1, 2, 3, 4, 5]; // assuming these are the category IDs
                  foreach ($category_ids as $category_id):
                    $category_name = ""; // Initialize category name
                    switch ($category_id) {
                      case 1:
                        $category_name = "Electronics";
                        break;
                      case 2:
                        $category_name = "Books";
                        break;
                      case 3:
                        $category_name = "Furnitures";
                        break;
                      case 4:
                        $category_name = "Clothing";
                        break;
                      case 5:
                        $category_name = "Toys";
                        break;
                    }
                  ?>

                    <div id="category-<?php echo $category_id; ?>" class="carousel-item-small mt-5 mb-5">
                      <div class="carousel-name text-white py-3 pe-4 rounded">
                        <h5 class="m-0"><?php echo $category_name; ?></h5>
                      </div>
                      <div id="carouselExampleSmall<?php echo $category_id; ?>" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner carousel-small">
                          <?php
                          // Fetch images and product names for the specific category
                          $activeClass = "active";
                          $imagesToShow = []; // Array to hold images for this category
                          foreach ($product_images as $image) {
                            if ($image['category_id'] == $category_id) {
                              $imagePath = $image['folder'];
                              $imagesToShow[] = $image; // Store the image path
                            }
                          }


                          for ($i = 0; $i < count($imagesToShow); $i += 3):
                          ?>
                            <div class="carousel-item <?php echo ($activeClass ? 'active' : ''); ?>">
                              <div class="d-flex justify-content-between"> <!-- Flexbox to align images horizontally -->
                                <?php for ($j = 0; $j < 3; $j++): ?>
                                  <?php if (isset($imagesToShow[$i + $j])): ?>
                                    <?php
                                    $productName = $imagesToShow[$i + $j]['product_name'];
                                    $productId = $imagesToShow[$i + $j]['product_id'];
                                    $imagePath = $imagesToShow[$i + $j]['folder'];
                                    $productDescription = $imagesToShow[$i + $j]['product_desc'];
                                    $productPrice = $imagesToShow[$i + $j]['product_price'];
                                    ?>
                                    <div class="card" style="width: 30%;">
                                      <img src="../../<?php echo $imagePath; ?>" class="card-img-top" alt="..." style="height: 300px; object-fit: cover;">
                                      <div class="card-body">
                                        <h5 class="card-title text-black"><?php echo $productName; ?></h5>
                                        <p class="card-text text-muted truncate"><?php echo $productDescription; ?></p> <!-- Product description with limited height -->
                                        <h6 class="text-black">Price: $ <?php echo number_format($productPrice, 2); ?></h6> <!-- Display the price -->
                                        <a href="product_details.php?product_id=<?php echo $productId; ?>" class="btn btn-primary btn-show-details w-100">Show Details</a>
                                      </div>
                                    </div>
                                  <?php endif; ?>
                                <?php endfor; ?>
                              </div>
                            </div>
                          <?php
                            $activeClass = "";
                          endfor;
                          ?>
                        </div> <!-- Close carousel-inner -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleSmall<?php echo $category_id; ?>" data-bs-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleSmall<?php echo $category_id; ?>" data-bs-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Next</span>
                        </button>
                      </div> <!-- Close carousel -->
                    </div> <!-- Close category carousel-item-small -->

                  <?php endforeach; ?>
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