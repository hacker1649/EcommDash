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

  <title>User Register Basic - Pages | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../public/assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <!-- Icons. Uncomment required icon fonts -->
  <link rel="stylesheet" href="../public/assets/vendor/fonts/boxicons.css" />

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
</head>

<body>

  <?php

  include "../db_connection/connection.php"; // Include the connection file

  // Start session to manage login
  session_start();

  // Check if the admin is already logged in
  if (isset($_SESSION['adminLoggedIn']) && $_SESSION['adminLoggedIn'] === true) {
    // Redirect to dashboard if user is already logged in
    header("Location: ../dashboard.php");
    exit;
  }

  // Check if the user is already logged in
  if (isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true) {
    // Redirect to dashboard if user is already logged in
    header("Location: dashboard.php");
    exit();
  }

  $firstName = $lastName = $email = $password = "";
  $fullNameErr = $lastNameErr = $emailErr = $passwordErr = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //retrieve the first name field 
    if (empty($_POST["firstname"])) {
      $firstNameErr = "First name is required";
    } else {
      $firstName = test_input($_POST["firstname"]);
      if (!preg_match("/^[a-zA-Z-' ]*$/", $firstName)) {
        $firstNameErr = "Only letters and white space allowed";
      }
    }
    //retrieve the last name field 
    if (empty($_POST["lastname"])) {
      $lastNameErr = "Last name is required";
    } else {
      $lastName = test_input($_POST["lastname"]);
      if (!preg_match("/^[a-zA-Z-' ]*$/", $lastName)) {
        $lastNameErr = "Only letters and white space allowed";
      }
    }
    // Retrieve user entered email
    if (empty($_POST["email"])) {
      $emailErr = "Email is required";
    } else {
      $email = trim($_POST["email"]);
      // Validate email format
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
      } else {
        // Check if email already exists in the database
        $query = "SELECT * FROM tbl_user WHERE user_email = '$email'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
          $emailErr = "User already exists with this email address";
        }
      }
    }
    //retrieve user entered password
    if (empty($_POST["password"])) {
      $passwordErr = "Password is required";
    } else {
      $password = trim($_POST["password"]);
      // Additional password checks (e.g., minimum length, etc.)
      if ((!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_])[A-Za-z\d@$_!%*?&]{8,}$/', $password))) {
        $passwordErr = "Password must be at least 8 characters long. <br> Contain atleast one lowercase, uppercase letters, numbers and special characters.";
      }
    }
    if (empty($firstNameErr) && empty($lastNameErr) && empty($emailErr) && empty($passwordErr)) {
      //insert all the data in the table
      $current_time = time();

      //applying sha-1 algorithm on the password before storing it into the database
      $hashedPassword = sha1($password);

      // Insert into tbl_user
      $insert_user = "INSERT INTO tbl_user (user_email, user_password, created_on, user_status) VALUES ('$email', '$hashedPassword', '$current_time', 1)";

      if ($conn->query($insert_user) === TRUE) {
        // Get the last inserted user_id
        $user_id = $conn->insert_id;

        // Insert into tbl_user_profile
        $insert_profile = "INSERT INTO tbl_user_profile (user_id, first_name, last_name, created_on) 
        VALUES ('$user_id', '$firstName', '$lastName', '$current_time')";

        if ($conn->query($insert_profile) === TRUE) {
          $success_message = "New user record added successfully...";
        } else {
          $error_message = "Error: " . $conn->error;
        }
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

  <!-- Content -->

  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Register Card -->
        <div class="card">
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

            <!-- Logo -->
            <div class="app-brand justify-content-center">
              <a href="index.html" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                  <svg
                    width="25"
                    viewBox="0 0 25 42"
                    version="1.1"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs>
                      <path
                        d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                        id="path-1"></path>
                      <path
                        d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                        id="path-3"></path>
                      <path
                        d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                        id="path-4"></path>
                      <path
                        d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                        id="path-5"></path>
                    </defs>
                    <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                        <g id="Icon" transform="translate(27.000000, 15.000000)">
                          <g id="Mask" transform="translate(0.000000, 8.000000)">
                            <mask id="mask-2" fill="white">
                              <use xlink:href="#path-1"></use>
                            </mask>
                            <use fill="#696cff" xlink:href="#path-1"></use>
                            <g id="Path-3" mask="url(#mask-2)">
                              <use fill="#696cff" xlink:href="#path-3"></use>
                              <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                            </g>
                            <g id="Path-4" mask="url(#mask-2)">
                              <use fill="#696cff" xlink:href="#path-4"></use>
                              <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                            </g>
                          </g>
                          <g
                            id="Triangle"
                            transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                            <use fill="#696cff" xlink:href="#path-5"></use>
                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                          </g>
                        </g>
                      </g>
                    </g>
                  </svg>
                </span>
                <span class="app-brand-text demo text-body fw-bolder">Sneat</span>
              </a>
            </div>
            <!-- /Logo -->

            <form id="formAuthentication" class="mb-3" action="" method="POST">
              <div class="mb-3">
                <label for="firstname" class="form-label">First Name <span class="error">*</span></label>
                <input
                  type="text"
                  class="form-control"
                  id="firstname"
                  name="firstname"
                  placeholder="Enter your first name"
                  autofocus />
                <span class="error">
                  <?php
                  if (isset($firstNameErr) && !empty($firstNameErr)) {
                    echo $firstNameErr;
                  }
                  ?>
                </span>
              </div>
              <div class="mb-3">
                <label for="lastname" class="form-label">Last Name <span class="error">*</span></label>
                <input
                  type="text"
                  class="form-control"
                  id="lastname"
                  name="lastname"
                  placeholder="Enter your last name"
                  autofocus />
                <span class="error">
                  <?php
                  if (isset($lastNameErr) && !empty($lastNameErr)) {
                    echo $lastNameErr;
                  }
                  ?>
                </span>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email <span class="error">*</span></label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" />
                <span class="error">
                  <?php
                  if (isset($emailErr) && !empty($emailErr)) {
                    echo $emailErr;
                  }
                  ?>
                </span>
              </div>
              <div class="mb-3 form-password-toggle">
                <label class="form-label" for="password">Password <span class="error">*</span></label>
                <div class="input-group input-group-merge">
                  <input
                    type="password"
                    id="password"
                    class="form-control"
                    name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
                <span class="error">
                  <?php
                  if (isset($passwordErr) && !empty($passwordErr)) {
                    echo $passwordErr;
                  }
                  ?>
                </span>
              </div>

              <div class="mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
                  <label class="form-check-label" for="terms-conditions">
                    I agree to
                    <a href="javascript:void(0);">privacy policy & terms</a>
                  </label>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100" type="submit">Sign up</button>
            </form>

            <p class="text-center">
              <span>Already have an account?</span>
              <a href="./login.php">
                <span>Sign in instead</span>
              </a>
            </p>
          </div>
        </div>
        <!-- Register Card -->
      </div>
    </div>
  </div>

  <!-- / Content -->

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