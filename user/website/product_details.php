<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Product Details - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>
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

    .product-gallery {
      position: relative;
      width: 100%;
      max-width: 600px;
      height: 500px;
      margin: 0 auto;
      overflow: hidden;
      border-radius: 8px;
      background-color: #fff;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      transition: all 0.3s ease;
    }

    .product-gallery img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 8px;
      transition: transform 0.3s ease;
    }

    .small-image {
      width: 90px;
      height: auto;
      object-fit: cover;
      margin-right: 10px;
      cursor: pointer;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .product-card {
      border: 1px solid #f1f1f1;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
      padding: 20px;
      background-color: #fff;
      transition: all 0.3s ease;
    }

    .price {
      color: red;
      font-weight: bold;
    }

    .small-images-container {
      display: flex;
      overflow-x: auto;
      gap: 15px;
      padding: 10px 0;
    }

    .small-images-container {
      display: flex;
      overflow-x: auto;
      gap: 15px;
      padding: 10px 0;
    }

    /* Basic styling for the price */
    .product-price {
      margin-top: 15px;
      font-size: 20px;
      font-weight: bold;
      color: #333;
      /* dark color for contrast */
    }

    /* Price in USD, can use font size adjustments for different devices */
    .price {
      font-size: 1.25rem;
      /* Bigger text for prominence */
      color: #e74c3c;
      /* Red color for attention */
    }
  </style>
</head>

<body>

  <?php

  include '../../db_connection/connection.php';
  session_start();

  // Check if product_id is set in the URL
  if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Query to fetch product details based on product_id
    $sql =
      "SELECT p.product_id, p.product_name, p.product_price, p.product_desc, c.category_name, pi.file_name, pi.folder, pi.img_priority
    FROM tbl_product p
    JOIN tbl_category c ON p.category_id = c.category_id
    LEFT JOIN tbl_product_img pi ON p.product_id = pi.product_id
    WHERE p.product_id = $product_id AND p.product_status = 1";

    $result = $conn->query($sql);

    // Fetch product details
    if ($result && $result->num_rows > 0) {
      $product = [];
      $images = [];
      while ($row = $result->fetch_assoc()) {
        $product['product_id'] = $row['product_id'];
        $product['product_name'] = $row['product_name'];
        $product['product_price'] = $row['product_price'];
        $product['product_desc'] = $row['product_desc'];
        $product['category_name'] = $row['category_name'];

        // Collect images and check priority
        if ($row['img_priority'] == 'H') {
          $product['main_image'] = $row['folder'];
        } else {
          $images[] = $row['folder'];
        }
      }
    } else {
      echo "Product not found.";
      exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if (!isset($_SESSION['userLoggedIn']) || $_SESSION['userLoggedIn'] !== true) {
        header("Location: ../login.php");
        exit;
      }
      $price = "SELECT product_price FROM tbl_product WHERE product_id=$product_id";
      $result = $conn->query($price);
      $product_price = $result->fetch_assoc()['product_price'];
      $user_id = $_SESSION['user_id'];
      $quantity = 1;
      $total_price = $product_price * $quantity;

      // Check if user has an active cart
      $sql_check_cart = "SELECT cart_id FROM tbl_cart WHERE user_id = $user_id AND cart_status = 1";
      $result_check_cart = $conn->query($sql_check_cart);

      if ($result_check_cart->num_rows > 0) {
        $cart_id = $result_check_cart->fetch_assoc()['cart_id'];
        $_SESSION['cart_id'] = $cart_id;
      } else {
        $current_time = time();
        $sql_create_cart = "INSERT INTO tbl_cart (user_id, cart_status, total_amount, created_on) VALUES ($user_id, 1, 0, $current_time)";
        if ($conn->query($sql_create_cart)) {
          $cart_id = $conn->insert_id;
          $_SESSION['cart_id'] = $cart_id;
        } else {
          die("Error creating cart: " . $conn->error);
        }
      }

      // Check if the product is already in the cart
      $sql_check_item = "SELECT quantity FROM tbl_cart_item WHERE cart_id = $cart_id AND product_id = $product_id";
      $result_check_item = $conn->query($sql_check_item);

      if ($result_check_item->num_rows > 0) {
        // Product exists in cart; update quantity and total price
        $current_quantity = $result_check_item->fetch_assoc()['quantity'];
        $new_quantity = $current_quantity + $quantity;
        $new_total_price = $product_price * $new_quantity;

        $sql_update_item = "UPDATE tbl_cart_item SET quantity = $new_quantity, t_product_price = $new_total_price WHERE cart_id = $cart_id AND product_id = $product_id";
        if (!$conn->query($sql_update_item)) {
          die("Error updating item quantity: " . $conn->error);
        }
      } else {
        $current_time = time();
        // Product does not exist in cart; add new item
        $sql_add_item = "INSERT INTO tbl_cart_item (cart_id, product_id, user_id, quantity, product_price, t_product_price, created_on) VALUES ($cart_id, $product_id, $user_id, $quantity, $product_price, $total_price, $current_time)";
        if (!$conn->query($sql_add_item)) {
          die("Error adding item to cart: " . $conn->error);
        }
      }
      // Update total amount in the cart
      $conn->query("UPDATE tbl_cart SET total_amount = total_amount + $total_price WHERE cart_id = $cart_id");
    }
  } else {
    echo "Product ID not provided.";
    exit; 
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
                <div class="container mt-5 mb-5">
                  <div class="row">
                    <!-- Product Main Image -->
                    <div class="col-md-6 d-flex justify-content-center mb-4 mb-md-0 p-0">
                      <div class="product-gallery">
                        <img id="main-image" src="../../<?php echo $product['main_image']; ?>" class="main-image" alt="...">
                      </div>
                    </div>

                    <!-- Product Details Section -->
                    <div class="col-md-6">
                      <div class="product-card">
                        <h3 class="fw-bold mb-4"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <div class="product-price">
                          <h6 class="price">$<span class="price-value"><?php echo number_format($product['product_price'], 2); ?></span></h6>
                        </div>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></p>
                        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>
                      </div>

                      <div class="mt-4">
                        <h5>Other Images</h5>
                        <div class="small-images-container d-flex align-items-center mb-3">
                          <!-- Add the main image as a clickable thumbnail -->
                          <img src="../../<?php echo $product['main_image']; ?>" class="small-image" alt="..." data-full-size="<?php echo $product['main_image']; ?>" onclick="changeMainImage('<?php echo $product['main_image']; ?>')">

                          <?php if (!empty($images)): ?>
                            <?php foreach ($images as $image): ?>
                              <img src="../../<?php echo $image; ?>" class="small-image" alt="..." data-full-size="<?php echo $image; ?>" onclick="changeMainImage('<?php echo $image; ?>')">
                            <?php endforeach; ?>
                          <?php else: ?>
                            <span>No additional images available.</span>
                          <?php endif; ?>
                        </div>
                      </div>

                      <form method="POST" action="">
                        <button  class="btn btn-primary mb-2 col-md-6 d-flex align-items-center justify-content-center">
                          <i class="bi bi-cart-plus me-2"></i> Add to Cart
                        </button>
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

  <script>
    // Function to change the main image when an additional image is clicked
    function changeMainImage(imagePath) {
      var mainImage = document.getElementById("main-image");
      // Prepend '../../' to the image path
      mainImage.src = "../../" + imagePath;
    }
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